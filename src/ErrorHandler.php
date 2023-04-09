<?php 
    class ErrorHandler {
        public static function handleError(
            int $errno,
            string $errstr,
            string $errfile,
            int $errline): void
        {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
        
        public static function handleException(Throwable $ex): void {
            echo json_encode([
                "errorMsg" => $ex->getMessage() . " line " . $ex->getLine(),
            ]);
        }
    }
?>