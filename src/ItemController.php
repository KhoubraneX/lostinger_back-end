<?php

class ItemController extends ItemGateway {

    public function processRequest(string $method , ?string $id): void {
        if ($id === null) {
                switch ($method) {
                    case 'GET':
                        $this->getItems();
                        break;
                        
                    case 'POST':
                        $userId = checkAuth();
                        $this->addItem($userId);
                        break;
                    
                    default:
                        notAllodMethods("GET , POST");
                        break;
                }
            } else {
                $id = htmlspecialchars($id);
                switch ($method) {
                    case 'GET':
                        $this->getItem($id);
                        break;
                        
                        case 'PATCH':
                            $userId = checkAuth();
                            $this->updateItem($id , $userId);
                            break;

                        case 'DELETE':
                            $userId = checkAuth();
                            $this->deleteItem($id , $userId);
                        break;
                    
                    default:
                        notAllodMethods("GET , DELETE , PATCH");
                        break;
                }
            }
        }
    }

?>