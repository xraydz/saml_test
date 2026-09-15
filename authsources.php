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

    // Auto-login as user1 with no credential prompt / login form shown.
    'autologin-user1' => array(
        'autologin:StaticUser',
        'attributes' => array(
            'uid' => array('1'),
            'eduPersonAffiliation' => array('group1'),
            'email' => array('user1@example.com'),
        ),
    ),

);
