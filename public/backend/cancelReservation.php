<?php
header('Content-Type: application/json');
if(isset($_POST['id'])){

    require('./db.php');

    $stmt = $conn->prepare("DELETE FROM `reservations` WHERE `id`=?");
    $stmt->bind_param("s", $_POST["id"]);
    $stmt->execute();
}
?>