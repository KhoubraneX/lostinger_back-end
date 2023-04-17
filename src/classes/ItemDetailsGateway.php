<?php
    class ItemDetailsGateway extends DataBase {

        public function ProvideItemDetails() {
            $sql = "SELECT * FROM item_category";
            $item_category = $this->executeQuery($sql);
            $item_category = $this->fetchAll($item_category);
            // 
            $sql = "SELECT * FROM item_place";
            $item_place = $this->executeQuery($sql);
            $item_place = $this->fetchAll($item_place);
            //
            $sql = "SELECT * FROM item_status";
            $item_status = $this->executeQuery($sql);
            $item_status = $this->fetchAll($item_status);
            //
            $sql = "SELECT * FROM item_type";
            $item_type = $this->executeQuery($sql);
            $item_type = $this->fetchAll($item_type);

            $itemDetails = [
                "item_category" => $item_category,
                "item_place" => $item_place,
                "item_status" => $item_status,
                "item_type" => $item_type
            ];

            echo(json_encode($itemDetails));
        }
    }
?>
