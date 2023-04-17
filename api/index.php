<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
  http_response_code(200);
  exit();
}

require dirname(__DIR__) . "/api/bootstarp.php";

$path = explode("/" , parse_url($_SERVER["REQUEST_URI"] , PHP_URL_PATH));

$resource = $path[3];
$id = $path[4] ?? null;
$method = $_SERVER["REQUEST_METHOD"];

if ($resource !== "items" && $resource !== "auth" && $resource !== "itemDetails") {
    notFound();
    exit;
}

switch ($resource) {
    case 'items':
        $controller = new ItemController();
        $controller->processRequest($method , $id);
        break;
    case 'itemDetails':
        $controller = new ItemDetailsController;
        $controller->processRequest($method , $id);
        break;
    case 'auth':
        $controller = new UserController;
        $controller->processRequest($method , $id);
        break;
}

?>