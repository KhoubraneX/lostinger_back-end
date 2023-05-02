<?php
class BlogGateway extends DataBase {
    public function getSimilarBlogs()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['_idBlog'])) {
            http_response_code(422);
            echo json_encode(["message" => "All fields required"]);
            exit;
        }

        $idItem = htmlspecialchars($data['_idBlog']);

        $sql = "SELECT * FROM blog WHERE _idBlog != '$idItem'";

        $data = $this->executeQuery($sql);
        $data = $this->fetchAll($data);

        foreach ($data as &$item) { // use reference &$item to update the original array
            if (isset($item['img']) && !empty($item['img'])) {
                $item['img'] = "http://" . $_SERVER['HTTP_HOST'] . "/space/img/items/" . $item['img'];
            }
        }

        print_r(json_encode($data));
    }

    public function getBlogs() {
        $data = $this->executeQuery("SELECT * FROM `blog`");
        $data = $this->fetchAll($data);

        foreach ($data as &$item) { // use reference &$item to update the original array
            if (isset($item['img']) && !empty($item['img'])) {
                $item['img'] = "http://" . $_SERVER['HTTP_HOST'] . "/space/img/items/" . $item['img'];
            }
        }

        print_r(json_encode($data));
    }

    public function getBlog(string $id)
    {
        $fetch = $this->executeQuery("SELECT *
      FROM 
        `blog` 
      WHERE _idBlog = '$id'");
        $data = $this->fetch($fetch);

        mysqli_num_rows($fetch) === 0 && notFound();
        if (isset($data['img']) && !empty($data['img'])) {
            $data['img'] = "http://" . $_SERVER['HTTP_HOST'] . "/space/img/items/" . $data['img'];
        }

        print_r(json_encode($data));
    }

    public function addBlog($userId)
    {
        $this->checkIfAdmin($userId);

        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['title'], $data['description'] , $data['img'])) {
            http_response_code(422);
            echo json_encode(["message" => "All fields required"]);
            exit;
        }
        //
        
        $_idBlog = uniqueID("blog_");
        $img = $data['img'];
        $title = htmlspecialchars($data['title']);
        $description = htmlspecialchars($data['description']);
        //
        if (empty($img) || $img == null) {
            $img = "";
        } else {
            $img  = imgToUrl($img);
        }
        //
        $sql = "INSERT INTO blog(_idBlog , title , description , img) 
                            VALUES ('$_idBlog' , '$title' , '$description' , '$img')";
        $res = $this->executeQuery($sql);
        //
        mysqli_affected_rows($this->connection) > 0 ? print_r(json_encode(["id" => $_idBlog, "message" => "success"])) : print_r(json_encode(["message" => "faild"]));
    }

    public function updateBlog(string $id, int $userId)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['like'])) {
            $sql = "UPDATE blog SET likeCounte = likeCounte + 1 WHERE _idBlog = '$id';";
            $this->executeQuery($sql);
            mysqli_affected_rows($this->connection) > 0 ? print_r(json_encode(["id" => $id, "message" => "updated successfully"])) : print_r(json_encode(["message" => "update faild"]));
            exit;
        }

        $this->checkIfadmin($userId);
        //
        $sql = "UPDATE blog SET ";
        foreach ($data as $col => $val) {
            $sql .= "$col='$val', ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= " WHERE _idBlog = '$id';";

        //
        $this->executeQuery($sql);
        mysqli_affected_rows($this->connection) > 0 ? print_r(json_encode(["id" => $id, "message" => "updated successfully"])) : print_r(json_encode(["message" => "update faild"]));
    }

    public function deleteBlog(string $id, $userId)
    {
        $this->checkIfadmin($userId);
        $this->executeQuery("DELETE FROM `blog` WHERE _idBlog = '$id' and _idUser = '$userId'");
        mysqli_affected_rows($this->connection) > 0 ? print_r(json_encode(array("message" => "deleted successfully"))) : notFound();
    }

    private function checkIfadmin($_idUser) {
        $sql = "SELECT _id , nameRole  FROM `user` INNER JOIN user_role USING(_idRole) WHERE _id = '$_idUser' AND nameRole = 'admin'";
        $data = $this->executeQuery($sql);
        if (mysqli_num_rows($data) === 0) {
            http_response_code(401);
            exit;
        }
    }
}
