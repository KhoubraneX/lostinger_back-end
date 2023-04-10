<?php

    class UserGateway extends DataBase {
        
        public function login() {
            
            $data = json_decode(file_get_contents("php://input") , true);

            if (!isset($data['email']) || !isset($data['password'])){
                $response = array(
                    'status' => 'error',
                    'message' => 'Email and password are required'
                );
                echo json_encode($response);
                http_response_code(400);
                exit;
            }

            $email = htmlspecialchars($data["email"]);
            $password = htmlspecialchars($data["password"]);
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Invalid email format'
                );
                echo json_encode($response);
                http_response_code(400);
                exit;
            }
            
            if (strlen($password) < 8) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Password must contain at least 8 characters'
                );
                echo json_encode($response);
                http_response_code(400);
                exit;
            }

            $sql = "SELECT _id , name , password FROM user WHERE email = '$email'";
            $user = $this->executeQuery($sql);

            if (mysqli_num_rows($user) === 0) {
                $response = array(
                    'status' => 'error',
                    'message' => "invalid authentication"
                );
                echo json_encode($response);
                http_response_code(401);
                exit;
            }

            $user = $this->fetch($user);
            $password_hash = $user["password"];
            if (!password_verify($password , $password_hash)){
                $response = array(
                    'status' => 'error',
                    'message' => "invalid authentication"
                );
                echo json_encode($response);
                http_response_code(401);
                exit;
            }

            $payload = [
                "sub" => $user["_id"],
                "name" =>  $user["name"],
                "email" => $email,
                "exp" => time() + 300
            ];

            $exp_ref = time() + 604800;
            $payload_ref = [
                "sub" => $user["_id"],
                "exp" => $exp_ref
            ];

            $JWTcode = new JWTCodec;
            $accessToken = $JWTcode->encode($payload);
            $refToken = $JWTcode->encode($payload_ref);

            // store refresh token in db
            $this->storeRefreshToken($refToken , $exp_ref);

            print_r(json_encode(["jwt" => $accessToken , "refresh_token" => $refToken]));
        }

        public function logout() {
            $data = json_decode(file_get_contents("php://input") , true);

            if (!isset($data['token'])){
                $response = array(
                    'message' => 'refresh token required'
                );
                echo json_encode($response);
                http_response_code(400);
                exit;
            }

            $refresh_token = $data['token'];

            // validate if token stored in db
            if ($this->getStoreToken($refresh_token) === false)  {
                http_response_code(400);
                echo json_encode(["message" => "invalid token (not in whitelist)"]);
                exit;
            }

            $JWTcode = new JWTCodec;
            $jwt = $JWTcode->decode_ref($refresh_token);

            // validate if token have a user
            $user = $this->getUser($jwt["_id"]);
            if (mysqli_num_rows($user) === 0) {
                notFound();
            }

            $this->deleteRefreshToken($refresh_token);
        }

        public function getUser(int $id) {
            $sql = "SELECT _id , name , email FROM user WHERE _id = '$id'";
            $user = $this->executeQuery($sql);
            return $user;
        }

        public function authAccessToken(): mixed {
            if (!isset(apache_request_headers()["Authorization"])) {
                http_response_code(400);
                echo json_encode(["message" => "Unauthorized"]);
                return false;
            }
            
            if (!preg_match("/^Bearer\s+(.*)$/" , apache_request_headers()["Authorization"] , $matches)) {
                http_response_code(400);
                echo json_encode(["message" => "Unauthorized"]);
                return false;
            }
            
            $JWTcode = new JWTCodec;
            $decoded = $JWTcode->decode($matches[1]);
            return $decoded["_id"];
        }

        public function refreshAccessToken() {
            $data = json_decode(file_get_contents("php://input") , true);

            if (!isset($data['token'])){
                $response = array(
                    'message' => 'refresh token required'
                );
                echo json_encode($response);
                http_response_code(400);
                exit;
            }

            $refresh_token = $data['token'];

            // validate if token stored in db
            if ($this->getStoreToken($refresh_token) === false)  {
                http_response_code(400);
                echo json_encode(["message" => "invalid token (not in whitelist)"]);
                exit;
            }

            $JWTcode = new JWTCodec;
            $jwt = $JWTcode->decode_ref($refresh_token);

            // validate if token have a user
            $user = $this->getUser($jwt["_id"]);
            if (mysqli_num_rows($user) === 0) {
                notFound();
            }

            $user = $this->fetch($user);

            $payload = [
                "sub" => $user["_id"],
                "name" =>  $user["name"],
                "email" => $user["email"],
                "exp" => time() + 300
            ];
            $exp_ref = time() + 604800;
            $payload_ref = [
                "sub" => $user["_id"],
                "exp" => $exp_ref 
            ];

            $JWTcode = new JWTCodec;
            $accessToken = $JWTcode->encode($payload);
            $refToken = $JWTcode->encode($payload_ref);

            // store refresh token from db
            $this->storeRefreshToken($refToken , $exp_ref);
            // delete store refresh token from db
            $this->deleteRefreshToken($refresh_token); //

            print_r(json_encode(["jwt" => $accessToken , "refresh_token" => $refToken]));
        }

        private function storeRefreshToken(string $refreshToken , string $exp) {
            $token_hash = hash_hmac("sha256" , $refreshToken , $_ENV["SECRET_KEY"]);
            $sql = "INSERT INTO refresh_token(token_hash , exp) VALUES ('$token_hash' , '$exp')";
            $res = $this->executeQuery($sql);
            var_dump(mysqli_affected_rows($this->connection));
            return $res;
        }

        private function deleteRefreshToken(string $refreshToken) {
            $token_hash = hash_hmac("sha256" , $refreshToken , $_ENV["SECRET_KEY"]);
            $sql = "DELETE FROM refresh_token WHERE token_hash = '$token_hash'";
            $this->executeQuery($sql);
        }

        private function getStoreToken(string $token) {
            $token_hash = hash_hmac("sha256" , $token , $_ENV["SECRET_KEY"]);
            $sql = "SELECT token_hash FROM refresh_token WHERE token_hash = '$token_hash'";
            $res = $this->executeQuery($sql);
            return mysqli_num_rows($res) !== 0 ? true : false;
        }
    }
?>
