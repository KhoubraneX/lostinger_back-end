<?php
    class UserController extends UserGateway {
        public function processRequest(string $method , ?string $id): void {
            if ($method !== "POST") notAllodMethods("POST");
            switch ($id) {
                case 'login':
                    $this->login();
                    break;
                case 'refresh':
                    $this->refreshAccessToken();
                    break;
                default:
                notFound();
                    break;
            }
        }
    }
?>