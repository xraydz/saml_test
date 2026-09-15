<?php

$config = array(

    'admin' => array(
        'core:AdminPassword',
    ),

    'example-userpass' => array(
        'exampleauth:UserPass',
        'user1:user1pass' => array(
            'uid' => array('1'),
            'eduPersonAffiliation' => array('group1'),
            'email' => 'user1@example.com',
        ),
        'user2:user2pass' => array(
            'uid' => array('2'),
            'eduPersonAffiliation' => array('group2'),
            'email' => 'user2@example.com',
        ),
    ),

    // Auto-login, no credential prompt / login form shown.
    // Attributes below are the identity this IdP will assert - swap 'email'
    // to change which identity is asserted on the next login.
    'autologin-user1' => array(
        'autologin:StaticUser',
        'attributes' => array(
            'uid' => array('1'),
            'eduPersonAffiliation' => array('group1'),
            'email' => array('ebca87885c02@tools.rande.xyz'),
        ),
    ),

);
