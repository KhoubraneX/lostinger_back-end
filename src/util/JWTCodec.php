<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    
    class JWTCodec {
        private $key;

        public function __construct()
        {
            $this->key = $_ENV["SECRET_KEY"];
        }

        public function encode(array $payload) {
            $jwt = JWT::encode($payload, $this->key, 'HS256');
            return $jwt;
        }
        
        public function decode(string $jwt) {
            try {
                // Verify and decode the JWT token
                $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));
            
                // Access the payload data
                $user_id = $decoded->sub;
                $user_name = $decoded->name;
                $user_email = $decoded->email;
                return ["_id" => $user_id  , "name" => $user_name , "email" => $user_email];

            } catch (Exception $e) {
                // Return an error response
                http_response_code(400);
                echo json_encode(["message" => "Token is invalid" . $e]);
                exit;
            }
        }
    }
?>