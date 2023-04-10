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

            $payload_ref = [
                "sub" => $user["_id"],
                "exp" => time() + 604800
            ];

            $JWTcode = new JWTCodec;
            $accessToken = $JWTcode->encode($payload);
            $refToken = $JWTcode->encode($payload_ref);

            print_r(json_encode(["jwt" => $accessToken , "refresh_token" => $refToken]));
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

            $JWTcode = new JWTCodec;
            $jwt = $JWTcode->decode_ref($refresh_token);

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

            $payload_ref = [
                "sub" => $user["_id"],
                "exp" => time() + 604800
            ];

            $JWTcode = new JWTCodec;
            $accessToken = $JWTcode->encode($payload);
            $refToken = $JWTcode->encode($payload_ref);

            print_r(json_encode(["jwt" => $accessToken , "refresh_token" => $refToken]));
        }
    }
?>
