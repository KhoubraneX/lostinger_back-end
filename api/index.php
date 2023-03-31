<?php

require dirname(__DIR__) . "/vendor/autoload.php";
require dirname(__DIR__) . "/src/functions/__built_in_functions.php";

// handel errors by json format
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$path = explode("/" , parse_url($_SERVER["REQUEST_URI"] , PHP_URL_PATH));

$resource = $path[3];
$id = $path[4] ?? null;
$method = $_SERVER["REQUEST_METHOD"];

if ($resource !== "items") {
    notFound();
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$db = mysqli_connect($_ENV["DB_HOST"] , $_ENV["DB_USER"] , $_ENV["DB_PASSWORD"] , $_ENV["DB_NAME"]);

switch ($resource) {
    case 'items':
        $controller = new ItemController;
        $controller->processRequest($method , $id);
        break;
}

?>