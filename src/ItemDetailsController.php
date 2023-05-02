<?php

class ItemDetailsController extends ItemDetailsGateway {

    public function processRequest(string $method , ?string $id): void {
        if ($id === null) {
                switch ($method) {
                    case 'GET':
                        $this->provideItemDetails();
                        break;
                    
                    default:
                        notAllodMethods("GET");
                        break;
                }
        } else {
            switch ($method) {
                case 'GET':
                    if ($id === "usedBrand") {
                        $this->provideMostUsedBrand();
                    }
                    break;
                
                default:
                    notAllodMethods("GET");
                    break;
            }
        }
    }
}

?>