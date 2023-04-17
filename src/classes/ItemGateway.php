<?php
    class ItemGateway extends DataBase {
        public function getItems() {
            $data = $this->executeQuery("SELECT 
            _idItem, 
            _idUser, 
            nameItem, 
            description, 
            location, 
            img, 
            creatAt, 
            date, 
            brand, 
            nameCategorie, 
            nameStatus, 
            nameType, 
            namePlace
          FROM 
            `item` 
            INNER JOIN item_category USING(_idCategory) 
            INNER JOIN item_status USING(_idStatus) 
            INNER JOIN item_type USING(_idType) 
            INNER JOIN item_place USING(_idPlace)
          ");
            $data = $this->fetchAll($data);

            foreach($data as &$item) { // use reference &$item to update the original array
                if(isset($item['img']) && !empty($item['img'])) {
                    $item['img'] = base64_encode(file_get_contents($item['img']));
                }
            }

            print_r(json_encode($data));
        }

        public function getItem(string $id) {
            $fetch = $this->executeQuery("SELECT * FROM `item` WHERE _idItem = '$id'");
            $data = $this->fetch($fetch);
            mysqli_num_rows($fetch) != 0 ? print_r(json_encode($data)) : notFound();
        }

        public function addItem($userId) {
            $data = json_decode(file_get_contents("php://input") , true);
            //
            $_idItem = uniqueID("item_");
            $_idUser = $userId;
            $nameItem = $data['nameItem'];
            $description = $data['description'];
            $location = $data['location'];
            $date = $data['date'];
            $img = $data['img'];
            $_idPlace = $data['_idPlace'];
            $_idCategory = $data['_idCategory'];
            $brand = $data['brand'];
            $_idType = $data['_idType'];
            //
            if (empty($nameItem) || strlen($nameItem) > 25) {
                unprocessableContent(["name" => "name most be lees than 25 charcter" ]);
                return;
            }
            //
            $sql = "INSERT INTO item(_idItem , _idUser, nameItem, description, location, date, img , _idPlace, _idCategory, brand, _idType) 
                            VALUES ('$_idItem' , '$_idUser', '$nameItem', '$description', '$location', '$date', '$img' , '$_idPlace', '$_idCategory', '$brand', '$_idType')";
            $res = $this->executeQuery($sql);
            //
            mysqli_affected_rows($this->connection) > 0 ? print_r(json_encode(["id" => $_idItem , "message" => "success" ])) : print_r(json_encode(["message" => "faild"]));
        }

        public function updateItem(string $id , int $userId) {
            $data = json_decode(file_get_contents("php://input") , true);
            //
            if (sizeof($data) === 0) return;
            if (isset($data["nameItem"])) {
                if (empty($data["nameItem"]) || strlen($data["nameItem"]) > 25) {
                    unprocessableContent(["name" => "name most be lees than 25 charcter" ]);
                    return;
                }
            }
            //
            $sql = "UPDATE item SET ";
            foreach ($data as $col => $val) {
                $sql .= "$col='$val', ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= " WHERE _idItem = '$id' AND _idUser = '$userId';";
            //
            $res = $this->executeQuery($sql);
            mysqli_affected_rows($this->connection) > 0 ? print_r(json_encode(["id" => $id , "message" => "updated successfully" ])) : print_r(json_encode(["message" => "faild"]));
        }

        public function deleteItem(string $id , $userId) {
            $this->executeQuery("DELETE FROM `item` WHERE _idItem = '$id' and _idUser = '$userId'");
            mysqli_affected_rows($this->connection) > 0 ? print_r(json_encode(array("message" => "deleted successfully"))) : notFound();
        }
    }
?>
