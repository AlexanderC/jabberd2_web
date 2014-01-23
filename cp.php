<?php

require "./config.php";
require "./controller.php";

$pdo = new PDO(sprintf("mysql:dbname=%s;host=%s", DB_NAME, DB_HOST), DB_USER, DB_PASS);
$controller = new Controller($pdo);

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "index";

$controller->execute($action);
