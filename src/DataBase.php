<?php

    class DataBase {
        public $connection;

        public function __construct() {
            try {
                $this->connection = mysqli_connect($_ENV["DB_HOST"] , $_ENV["DB_USER"] , $_ENV["DB_PASSWORD"] , $_ENV["DB_NAME"]);
                // Configure to prevent numeric values from being converted to strings
                mysqli_options($this->connection , MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
            }catch (mysqli_sql_exception $e) {
                http_response_code("-7001");
                print_r(json_encode(["errorMsg" =>  "incorrect database setting"]));
                exit;
            }
        }

        public function executeQuery(string $query): mixed {
            try {
                return mysqli_query($this->connection , $query);
                $this->destruct();
            } catch (mysqli_sql_exception $e) {
                print_r(json_encode(["errorMsg" =>  "$e"]));
                exit;
            }
        }

        public function destruct(): void{
            mysqli_close($this->connection);
        }

        public function fetchAll($data): array | null {
            $data = mysqli_fetch_all($data , MYSQLI_ASSOC);
            return isset($data) ? $data : notFound();
        }

        public function fetch($data): array | null {
            $data = mysqli_fetch_assoc($data);
            return isset($data) ? $data : notFound();
        }
        

    }
?>