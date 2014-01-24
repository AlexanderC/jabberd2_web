<?php

namespace Jabberd2;

if(in_array($_SERVER["REMOTE_ADDR"], array("127.0.0.1","::1"))) {
    require "../pretty-exceptions/loader.php";
} else {
    error_reporting(0);
}

require "../app/autoloader.php";

$config = require("../app/config.php");

if(!is_array($config)) {
    throw new \RuntimeException("Configuration file MUST return an array!");
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "index";

$pdoConnection = new PdoConnection($config);
$controller = new ApplicationController($pdoConnection->getConnection());

$response = $controller->execute($action);
$response->flush();
