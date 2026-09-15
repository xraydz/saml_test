<?php

/**
 * Auto-login authentication source.
 *
 * Immediately completes authentication with a fixed set of attributes,
 * with no login form / credential prompt shown to the user at all.
 */
class sspmod_autologin_Auth_Source_StaticUser extends SimpleSAML_Auth_Source {

	private $attributes;

	public function __construct($info, $config) {
		assert('is_array($info)');
		assert('is_array($config)');

		parent::__construct($info, $config);

		$this->attributes = isset($config['attributes']) ? $config['attributes'] : array(
			'uid' => array('1'),
			'eduPersonAffiliation' => array('group1'),
			'email' => array('user1@example.com'),
		);
	}

	public function authenticate(&$state) {
		assert('is_array($state)');

		$state['Attributes'] = $this->attributes;

		self::completeAuth($state);
	}

}
