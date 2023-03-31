<?php
    require dirname(__DIR__) . "/src/DataBase.php";
    require dirname(__DIR__) . "/src/classes/ItemGateway.php";

    class ItemController extends ItemGateway {
        public function processRequest(string $method , ?string $id): void {
            if ($id === null) {
                switch ($method) {
                    case 'GET':
                        $this->getItems();
                        break;

                    case 'POST':
                        $this->addItem();
                        break;
                        
                    case 'POST':
                        $this->addItem();
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
                    case 'DELETE':
                        $this->deleteItem($id);
                        break;
                    
                    default:
                        notAllodMethods("GET , DELETE , UPDATE");
                        break;
                }
            }
        }
    }

?>