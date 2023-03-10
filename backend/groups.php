<?php require_once("config.php") ?>

<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods:*");
$object = new crud;
$conn = $object->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $text = $_POST["text"];
    $user_id = $_POST['user_id'];
    $file = $_FILES["file"];

    print_r($_POST);
    print_r($text);
    print_r($user_id);
    print_r($file);
  
    $targetDir = "../frontend/src/components/images/";
    $fileName = basename($file["name"]);
    $targetPath = $targetDir . $fileName;
  
    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
      echo "File uploaded successfully";
        $sql = "INSERT INTO groups (user_id , group_name , group_image)
                VALUES ( ? , ? , ? )" ;
        $query = $conn->prepare($sql);
        $query->execute([ $user_id , $text , $fileName]);
    } else {
      echo "Error uploading file";
    }
  
  } elseif ($_SERVER["REQUEST_METHOD"] === "GET") {

    $sql = "SELECT * 
            FROM groups
            INNER JOIN users WHERE groups.user_id = users.id" ;
    $query = $conn->prepare($sql);
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);

  } elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $sql = "DELETE FROM groups WHERE group_id = ?" ;
    $path = explode('/' , $_SERVER['REQUEST_URI']);
    print_r($path, 'path');
    if(isset($path[5]) && is_numeric($path[5])){
        $query = $conn->prepare($sql);
        $query->execute([$path[5]]);
    }
  }