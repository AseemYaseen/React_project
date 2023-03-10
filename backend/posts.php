<?php include 'config.php'; ?>

<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json;");



$method = $_SERVER['REQUEST_METHOD'];

$con=crud::connect();
switch ($method) {
    case 'GET' :
        $sql = "SELECT * FROM `users`
                INNER JOIN `posts` ON posts.user_id = users.id
                ORDER BY posts.created_at DESC" ;
        $query = $con->prepare($sql);
        $query->execute();
        $posts = $query->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($posts);
        break;

    case 'POST' :

        $text = $_POST["post"];
        $user_id = $_POST['user_id'];
        if($_FILES["file"] == null){
        $file = "";
        } else {
            $file = $_FILES["file"] ;
        }

        if($_POST["group_id"] == null){
        $group_id = "";
        } else {
            $group_id = $_POST["group_id"] ;
        }

        if($file != ""){
            $targetDir = "../frontend/src/components/images/";
            $fileName = basename($file["name"]);
            $targetPath = $targetDir . $fileName;
            
            if (move_uploaded_file($file["tmp_name"], $targetPath)) {
            echo "File uploaded successfully";
                $sql = "INSERT INTO posts (user_id , content , post_image, group_id)
                        VALUES ( ? , ? , ? , ?)" ;
                $query = $con->prepare($sql);
                $query->execute([$user_id , $text , $fileName, $group_id ]);
                break;
            } else {
            echo "Error uploading file";
            }
        } else {
            $sql = "INSERT INTO posts (user_id , content )
                    VALUES ( ? , ? )" ;
            $query = $con->prepare($sql);
            $query->execute([$user_id , $text ]);
            break;
        }



    case 'DELETE' :
        $sql = "DELETE FROM posts WHERE post_id = ?" ;
        $path = explode('/' , $_SERVER['REQUEST_URI']);
        if(isset($path[5]) && is_numeric($path[5])){
            $query = $con->prepare($sql);
            $query->execute([$path[5]]);
        }
        break;
}