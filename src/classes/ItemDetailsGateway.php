<?php
    class ItemDetailsGateway extends DataBase {

        public function provideItemDetails() {
            $sql = "SELECT c._idCategory , c.nameCategorie, IFNULL(COUNT(i._idItem), 0) AS item_count
            FROM item_category c
            LEFT JOIN item i ON i._idCategory = c._idCategory
            GROUP BY c.nameCategorie";
            $item_category = $this->executeQuery($sql);
            $item_category = $this->fetchAll($item_category);
            // 
            $sql = "SELECT p._idPlace , p.namePlace, IFNULL(COUNT(i._idItem), 0)  AS item_count
            FROM item_place p
            LEFT JOIN item i ON i._idPlace = p._idPlace
            GROUP BY p.namePlace";
            $item_place = $this->executeQuery($sql);
            $item_place = $this->fetchAll($item_place);
            //
            $sql = "SELECT s._idStatus , s.nameStatus, IFNULL(COUNT(i._idItem), 0) AS item_count
            FROM item_status s
            LEFT JOIN item i ON i._idPlace = s._idStatus
            GROUP BY s.nameStatus";
            $item_status = $this->executeQuery($sql);
            $item_status = $this->fetchAll($item_status);
            //
            $sql = "SELECT t._idType , t.nameType, IFNULL(COUNT(i._idItem), 0) AS item_count
            FROM item_type t
            LEFT JOIN item i ON i._idType = t._idType
            GROUP BY t.nameType";
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

        public function provideMostUsedBrand() {
            $sql = "SELECT brand, COUNT(*) AS brand_count
            FROM item
            WHERE brand IS NOT NULL
            GROUP BY brand
            ORDER BY brand_count DESC
            LIMIT 10";

            $item_brand = $this->executeQuery($sql);
            $item_brand = $this->fetchAll($item_brand);

            echo(json_encode($item_brand));
        }
    }
?>
