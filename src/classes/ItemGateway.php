<?php
class ItemGateway extends DataBase
{

    public function getItemsByid($userId) {
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
            WHERE  _idUser = '$userId'
          ");
        $data = $this->fetchAll($data);

        foreach ($data as &$item) { // use reference &$item to update the original array
            if (isset($item['img']) && !empty($item['img'])) {
                $item['img'] = "http://" . $_SERVER['HTTP_HOST'] . "/space/img/items/" . $item['img'];
            }
        }

        print_r(json_encode($data));
    }
    public function getSimilarItem()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['_idItem'], $data['nameCategorie'])) {
            http_response_code(422);
            echo json_encode(["message" => "All fields required"]);
            exit;
        }

        $idItem = htmlspecialchars($data['_idItem']);
        $Category = htmlspecialchars($data['nameCategorie']);

        $sql = "SELECT _idItem, 
        nameItem, 
        description, 
        location, 
        img, 
        date, 
        brand, 
        nameCategorie, 
        nameStatus, 
        nameType, 
        namePlace
    FROM item
    INNER JOIN item_category USING(_idCategory) 
    INNER JOIN item_status USING(_idStatus) 
    INNER JOIN item_type USING(_idType) 
    INNER JOIN item_place USING(_idPlace)
        WHERE _idItem != '$idItem'
            AND nameCategorie = '$Category'
               ";

        $data = $this->executeQuery($sql);
        $data = $this->fetch($data);

        if (isset($data['img']) && !empty($data['img'])) {
            $data['img'] = "http://" . $_SERVER['HTTP_HOST'] . "/space/img/items/" . $data['img'];
        }

        print_r(json_encode($data));
    }
    public function getItems()
    {
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

        foreach ($data as &$item) { // use reference &$item to update the original array
            if (isset($item['img']) && !empty($item['img'])) {
                $item['img'] = "http://" . $_SERVER['HTTP_HOST'] . "/space/img/items/" . $item['img'];
            }
        }

        print_r(json_encode($data));
    }

    public function getItemsNear($city)
    {
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
            WHERE location like '%$city%'
        ");

        // if there is no data match in that city
        if (mysqli_num_rows($data) < 1) {
            $this->getItems();
            exit;
        }

        $data = $this->fetchAll($data);
        foreach ($data as &$item) { // use reference &$item to update the original array
            if (isset($item['img']) && !empty($item['img'])) {
                $item['img'] = "http://" . $_SERVER['HTTP_HOST'] . "/space/img/items/" . $item['img'];
            }
        }

        print_r(json_encode($data));
    }

    public function getItem(string $id)
    {
        $fetch = $this->executeQuery("select _idItem, 
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
        namePlace,
        createAt,
        name,
        email,
        phone
      FROM 
        `item` 
        INNER JOIN item_category USING(_idCategory) 
        INNER JOIN item_status USING(_idStatus) 
        INNER JOIN item_type USING(_idType) 
        INNER JOIN item_place USING(_idPlace)
        INNER JOIN user on user._id = item._idUser
         
      WHERE _idItem = '$id'");
        $data = $this->fetch($fetch);

        mysqli_num_rows($fetch) === 0 && notFound();
        if (isset($data['img']) && !empty($data['img'])) {
            $data['img'] = "http://" . $_SERVER['HTTP_HOST'] . "/space/img/items/" . $data['img'];
        }

        print_r(json_encode($data));
    }

    public function addItem($userId)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['nameItem'], $data['description'], $data['date'], $data['location'], $data['_idPlace'], $data['_idCategory'], $data['brand'], $data['_idType'])) {
            http_response_code(422);
            echo json_encode(["message" => "All fields required"]);
            exit;
        }
        //
        $_idItem = uniqueID("item_");
        $_idUser = $userId;
        $nameItem = htmlspecialchars($data['nameItem']);
        $description = htmlspecialchars($data['description']);
        $location = htmlspecialchars($data['location']);
        $date = htmlspecialchars($data['date']);
        $img = $data['img'] ?? null;
        $_idPlace = htmlspecialchars($data['_idPlace']);
        $_idCategory = htmlspecialchars($data['_idCategory']);
        $brand = htmlspecialchars($data['brand']);
        $_idType = htmlspecialchars($data['_idType']);
        //
        $this->checkItemData($nameItem, $description, $location, $brand, $date, $_idPlace, $_idCategory, $_idType);
        //
        if (empty($img) || $img == null) {
            $img = "";
        } else {
            $img  = imgToUrl($img);
        }
        //
        $sql = "INSERT INTO item(_idItem , _idUser, nameItem, description, location, date, img , _idPlace, _idCategory, brand, _idType) 
                            VALUES ('$_idItem' , '$_idUser', '$nameItem', '$description', '$location', '$date', '$img' , '$_idPlace', '$_idCategory', '$brand', '$_idType')";
        $res = $this->executeQuery($sql);
        //
        mysqli_affected_rows($this->connection) > 0 ? print_r(json_encode(["id" => $_idItem, "message" => "success"])) : print_r(json_encode(["message" => "faild"]));
    }

    public function updateItem(string $id, int $userId)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        //
        if (empty($data) || sizeof($data) === 0) return;
        if (isset($data["nameItem"])) {
            $this->checkItemData($data["nameItem"], "A", "A", "A", "01-10-1900", 0, 0, 0);
        }
        if (isset($data["description"])) {
            $this->checkItemData("A", $data["description"], "A", "A", "01-10-1900", 0, 0, 0);
        }
        if (isset($data["location"])) {
            $this->checkItemData("A", "A", $data["location"], "A", "01-10-1900", 0, 0, 0);
        }
        if (isset($data["brand"])) {
            $this->checkItemData("A", "A", "A", $data["brand"], "01-10-1900", 0, 0, 0);
        }
        if (isset($data["date"])) {
            $this->checkItemData("A", "A", "A", "A", $data["date"], 0, 0, 0);
        }
        if (isset($data["_idPlace"])) {
            $this->checkItemData("A", "A", "A", "A", "01-10-1900", $data["_idPlace"], 0, 0);
        }
        if (isset($data["_idCategory"])) {
            $this->checkItemData("A", "A", "A", "A", "01-10-1900", 0, $data["_idCategory"], 0);
        }
        if (isset($data["_idType"])) {
            $this->checkItemData("A", "A", "A", "A", "01-10-1900", 0, 0, $data["_idType"]);
        }
        //
        $sql = "UPDATE item SET ";
        foreach ($data as $col => $val) {
            $sql .= "$col='$val', ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= " WHERE _idItem = '$id' AND _idUser = '$userId';";

        //
        $this->executeQuery($sql);
        mysqli_affected_rows($this->connection) > 0 ? print_r(json_encode(["id" => $id, "message" => "updated successfully"])) : print_r(json_encode(["message" => "update faild"]));
    }

    public function deleteItem(string $id, $userId)
    {
        $this->executeQuery("DELETE FROM `item` WHERE _idItem = '$id' and _idUser = '$userId'");
        mysqli_affected_rows($this->connection) > 0 ? print_r(json_encode(array("message" => "deleted successfully"))) : notFound();
    }

    private function checkItemData($nameItem, $description, $location, $brand, $date, $_idPlace, $_idCategory, $_idType)
    {
        if (!preg_match("/^.{1,24}$/", $nameItem)) {
            unprocessableContent(["name" => "name most be between 1 and 25 characters long"]);
        }
        if (!preg_match("/^.{1,255}$/", $description)) {
            unprocessableContent(["description" => "description most be between 1 and 255 characters long"]);
        }
        if (!preg_match("/^.{1,100}$/", $location)) {
            unprocessableContent(["location" => "location is not valid"]);
        }
        if (!preg_match("/^.{1,20}$/", $brand)) {
            unprocessableContent(["brand" => "brand most be brand between 1 and 10 characters long"]);
        }
        if (!isset($date) || !empty($date)) {
            $timestamp = strtotime($date);
            if ($timestamp !== false) {
                $inputDateObj = new DateTime($date);
                $testCheck = $inputDateObj->getTimestamp() <= time();
                if (!$testCheck) {
                    unprocessableContent(["date" => "date must be less than or equal to the current date"]);
                }
            } else {
                unprocessableContent(["date" => "The value is not a valid date"]);
            }
        } else {
            unprocessableContent(["brand" => "date is required"]);
        }
        if (!is_numeric($_idPlace) || !is_numeric($_idCategory) || !is_numeric($_idType)) {
            unprocessableContent(["Place-Category-Type" => "invalid data Place, Category, Type"]);
        }
    }
}
