<?php

return array(
    'db' => array(
        'host' => 'localhost', // database host
        'name' => 'jabberd2', // database name
        'user' => 'root', // database user
        'pass' => '' // database password
    ),
    'xmpp' => array(
        'address' => 'tcp://chat.flosites.com:5222', // xmpp server address
        'username' => 'ross.tanner', // xmpp admin user username
        'password' => 'WQdm7gQU9oeGUC3GnotT', // xmpp admin user password
        'alias' => 'Ross Tanner', // xmpp admin user alias (used as groupchat name)
        'mucdir' => '/var/spool/mu-conference/rooms' // Mu Conference Jabberd2 Plugin rooms directory
    ),
    'log_dir' => __DIR__ . "/../logs"
);
