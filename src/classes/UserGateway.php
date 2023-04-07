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

            $query = "SELECT _id , name , password FROM user WHERE email = '$email'";
            $user = $this->executeQuery($query);

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
                "email" => $email
            ];

            $JWTcode = new JWTCodec;
            $accessToken = $JWTcode->encode($payload);

            print_r(json_encode(["accessToken" => $accessToken]));
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

            $accessToken = base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $matches[1])[1])));

            if ($accessToken === false) {
                http_response_code(400);
                echo json_encode(["message" => "Unauthorized"]);
                return false;
            }
            
            $data = json_decode($accessToken , true);
            
            if ($data === null) {
                http_response_code(400);
                echo json_encode(["message" => "invalid Json"]);
                return false;
            }

            return $data;
        }
    }
?>
