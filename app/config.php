<?php

return array(
    'db' => array(
        'host' => 'localhost', // database host
        'name' => 'jabberd2', // database name
        'user' => 'root', // database user
        'pass' => '' // database password
    ),
    'xmpp' => array(
        'address' => 'tcp://chat.example.com:5222', // xmpp server address
        'username' => 'john.doe', // xmpp admin user username
        'password' => 'secret', // xmpp admin user password
        'alias' => 'Doe John', // xmpp admin user alias (used as groupchat name)
        'mucdir' => '/var/spool/mu-conference/rooms' // Mu Conference Jabberd2 Plugin rooms directory
    ),
    'log_dir' => __DIR__ . "/../logs"
);
