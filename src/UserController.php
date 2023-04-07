<?php
    class UserController extends UserGateway {
        public function processRequest(string $method , ?string $id): void {
            if ($id === "login") {
                $id = htmlspecialchars($id);
                //
                    if ($method === "POST") {
                        $this->login();
                    } else {
                        notAllodMethods("POST");
                    }
                //
                } else {
                    notFound();
                }
            }
        }
?>