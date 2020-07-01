<?php

use App\Controllers\DataController;

require_once '../bootstrap.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ($uri[1] !== 'data') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// the user id is, of course, optional and must be a number:
$table = null;
if (isset($uri[2])) {
    $table = $uri[2];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

$controller = new DataController($dbConnection, $requestMethod, $table);
$controller->processRequest();