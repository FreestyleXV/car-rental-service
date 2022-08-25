<?php
if(isset($_POST['login'], $_POST['mail'], $_POST['password'])){

    require('db.php');

    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `name`=?");
    $stmt->bind_param("s", $_POST['login']); // 's' specifies the variable type => 'string'
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){   
        echo '["not"]';
    }
    else{
        $stmt = $conn->prepare("INSERT INTO `users` (`name`, `mail`, `password`, `permissions`) VALUES (?, ?, PASSWORD(?), 'deactive')");
        $stmt->bind_param("sss", $_POST['login'], $_POST['mail'], $_POST['password']);
        $stmt->execute();

        $stmt = $conn->prepare("SELECT id FROM `users` WHERE `name`=?");
        $stmt->bind_param("s", $_POST['login']); // 's' specifies the variable type => 'string'
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id);
        $stmt->fetch();
        echo json_encode(["ok", $id]);
        $stmt->close();
    }
    
    $conn->close();
}
else{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Requset");
}
?>