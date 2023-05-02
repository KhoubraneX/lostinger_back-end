<?php
require_once  dirname(__DIR__) . "/vendor/autoload.php";
require dirname(__DIR__) . "/src/functions/__built_in_functions.php";
require dirname(__DIR__) . "/src/util/JWTCodec.php";
require dirname(__DIR__) . "/src/classes/ItemGateway.php";
require dirname(__DIR__) . "/src/classes/ItemDetailsGateway.php";
require dirname(__DIR__) . "/src/classes/UserGateway.php";
require dirname(__DIR__) . "/src/classes/BlogGateway.php";

// handel errors by json format
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

?>