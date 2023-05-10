<?php
class UserController extends UserGateway
{
    public function processRequest(string $method, ?string $id): void
    {
        if ($method === "POST") {
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
        } elseif ($method === "GET") {
            switch ($id) {
                case 'userDt':
                    $userId = checkAuth();
                    $this->getUserDetails($userId);
                    break;
                default:
                    notFound();
                    break;
            }
        } elseif ($method === "PATCH") {
            switch ($id) {
                case 'updateUser':
                    $userId = checkAuth();
                    $this->UpdateUser($userId);
                    break;
                default:
                    notFound();
                    break;
            }
        } else {
            notAllodMethods("GET , PATCH , POST");
        }
    }
}
