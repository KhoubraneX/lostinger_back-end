<?php
    class UserController extends UserGateway {
        public function processRequest(string $method , ?string $id): void {
            if ($method !== "POST") notAllodMethods("POST");
            switch ($id) {
                case 'login':
                    $this->login();
                    break;
                case 'register':
                    $this->register();
                    break;
                case 'logout':
                    $this->logout();
                    break;
                case 'refresh':
                    $this->refreshAccessToken();
                    break;
                case 'check':
                    $this->CheckAccessToken();
                    break;
                default:
                notFound();
                    break;
            }
        }
    }
?>