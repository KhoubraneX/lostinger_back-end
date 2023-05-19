<?php

class ItemController extends ItemGateway {

    public function processRequest(string $method , ?string $id): void {
        if ($id === null) {
                switch ($method) {
                    case 'GET':
                        if (isset($_GET["target"]) && $_GET["target"] == "myItems") {
                            $userId = checkAuth();
                            $this->getItemsByid($userId);
                        } else {
                            $this->getItems();
                        }
                        break;
                        
                    case 'POST':
                        if (isset($_GET["target"]) && $_GET["target"] === "similarItem") {
                            $this->getSimilarItem();
                        } else {
                            $userId = checkAuth();
                            $this->addItem($userId);
                        }
                        break;
                    
                    default:
                        notAllodMethods("GET , POST");
                        break;
                }
            } else {
                $id = htmlspecialchars($id);
                switch ($method) {
                    case 'GET':
                        if ($id === "search" && isset($_GET["city"])) {
                            $this->getItemsNear($_GET["city"]);
                        } else if (isset($_GET["target"]) && $_GET["target"] == "editMyItem") {
                            $userId = checkAuth();
                            $this->getItemById($id , $userId);
                        }
                        else {
                            $this->getItem($id);
                        }
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