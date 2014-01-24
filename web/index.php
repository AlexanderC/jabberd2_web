<?php

namespace Jabberd2;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

$loader = require "../vendor/autoload.php";

if(in_array($_SERVER["REMOTE_ADDR"], array("127.0.0.1","::1"))) {
    error_reporting(-1);
    $run = new Run();
    $handler = new PrettyPageHandler();

    $run->pushHandler($handler);
    $run->register();
} else {
    error_reporting(0);
}

$configData = require("../app/config.php");

if(!is_array($configData)) {
    throw new \RuntimeException("Configuration file MUST return an array!");
}

Config::setData($configData, true);
unset($configData);

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "index";

$pdoConnection = new PdoConnection();
$controller = new ApplicationController(
    $pdoConnection->getConnection(),
    new Xmpp()
);

$response = $controller->execute($action);
$response->flush();
