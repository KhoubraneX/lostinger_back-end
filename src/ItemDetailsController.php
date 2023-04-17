<?php

class ItemDetailsController extends ItemDetailsGateway {

    public function processRequest(string $method , ?string $id): void {
        if ($id === null) {
                switch ($method) {
                    case 'GET':
                        $this->ProvideItemDetails();
                        break;
                    
                    default:
                        notAllodMethods("GET");
                        break;
                }
        } else {
            // 
        }
    }
}

?>