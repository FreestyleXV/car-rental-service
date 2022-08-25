<?php
if(isset($_POST['user'], $_POST['car'], $_POST['startDate'], $_POST['finishDate'], $_POST['cost'])){

    require('db.php');

    $stmt = $conn->prepare("SELECT `id` FROM `users` WHERE `name`= ?");
    $stmt->bind_param("s", $_POST['user']); // 's' specifies the variable type => 'string'
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){   
        $stmt->bind_result($userId);
        $stmt->fetch();
        $stmt = $conn->prepare("INSERT INTO `reservations`(`user_id`, `car_id`, `start_date`, `finish_date`, `cost`, `status`) VALUES (?,?,?,?,?,'p')");
        $stmt->bind_param("iissi", $userId, $_POST['car'], $_POST['startDate'], $_POST['finishDate'], $_POST['cost']);
        $stmt->execute();
        $stmt->close();
        echo "OK";
    }
    else{
        echo "NOT";
    }
    
    $conn->close();
}
else{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Requset");
}
?>