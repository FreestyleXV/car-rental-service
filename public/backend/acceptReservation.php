<?php
header('Content-Type: application/json');
if(isset($_POST['id'], $_POST['car'])){

    require('./db.php');

    $stmt = $conn->prepare("UPDATE `reservations` SET `status`='a' WHERE `id`=?");
    $stmt->bind_param("s", $_POST["id"]);
    $stmt->execute();
    
    $stmt = $conn->prepare("DELETE FROM `reservations` WHERE `car_id`=? AND `status`='p'");
    $stmt->bind_param("s", $_POST["car"]);
    $stmt->execute();
}
?>