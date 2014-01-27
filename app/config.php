<?php

return array(
    'db' => array(
        'host' => 'localhost',
        'name' => 'jabberd2',
        'user' => 'root',
        'pass' => ''
    ),
    'xmpp' => array(
        'address' => 'tcp://chat.flosites.com:5222',
        'username' => 'ross.tanner',
        'password' => 'WQdm7gQU9oeGUC3GnotT',
        'alias' => 'Ross Tanner',
        'mucdir' => __DIR__ . '/../tmp/rooms' // mu-conference folder
    ),
    'log_dir' => __DIR__ . "/../logs"
);
