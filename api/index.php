<?php

require __DIR__ . "\bootstarp.php";

$path = explode("/" , parse_url($_SERVER["REQUEST_URI"] , PHP_URL_PATH));

$resource = $path[3];
$id = $path[4] ?? null;
$method = $_SERVER["REQUEST_METHOD"];

if ($resource !== "items" && $resource !== "auth") {
    notFound();
    exit;
}

switch ($resource) {
    case 'items':
        $controller = new ItemController;
        $controller->processRequest($method , $id);
        break;
    case 'auth':
        $controller = new UserController;
        $controller->processRequest($method , $id);
        break;
}

?>