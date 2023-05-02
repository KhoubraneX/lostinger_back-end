<?php

class BlogController extends BlogGateway {

    public function processRequest(string $method , ?string $id): void {
        if ($id === null) {
                switch ($method) {
                    case 'GET':
                            $this->getBlogs();
                        break;

                    case 'POST':
                        if (isset($_GET["target"]) && $_GET["target"] === "similarBlogs") {
                            $this->getSimilarBlogs();
                        } else {
                            $userId = checkAuth();
                            $this->addBlog($userId);
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
                            $this->getBlog($id);
                        break;

                        case 'PATCH':
                            $userId = checkAuth();
                            $this->updateBlog($id , $userId);
                            break;

                        case 'DELETE':
                            $userId = checkAuth();
                            $this->deleteBlog($id , $userId);
                        break;
                    
                    default:
                        notAllodMethods("GET , DELETE , PATCH , POST");
                        break;
                }
            }
        }
    }
