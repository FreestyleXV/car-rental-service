<?php
header('Content-Type: application/json');
if(isset($_POST['id'], $_POST['permission'])){

    require('./db.php');

    $stmt = $conn->prepare("UPDATE `users` SET `permissions`=? WHERE `id`=?");
    $stmt->bind_param("ss", $_POST['permission'], $_POST["id"]);
    $stmt->execute();
}
?>