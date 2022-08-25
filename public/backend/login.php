<?php
if(isset($_POST['login'], $_POST['password'])){

    $login = $_POST['login'];
    $password = $_POST['password'];

    require('./db.php');
    $stmt = $conn->prepare("SELECT `id`, `permissions` FROM `users` WHERE `name`=? AND `password`=PASSWORD(?)");
    $stmt->bind_param("ss", $login, $password);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){
        $stmt->bind_result($id, $p);

        while($stmt->fetch()){
            echo json_encode(["ok", $p, $id]);
        }
    }
    else{
        echo json_encode(["not"]);
    }
    $stmt->close();
    $conn->close();
}
else{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Requset");
}
?>