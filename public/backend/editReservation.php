<?php
header('Content-Type: application/json');
if(isset($_POST['id'], $_POST['date'], $_POST['status'])){

    require('./db.php');


    $currentDate = date('Y-m-d');
    $status = $_POST['status'];
    if($status == 'p'){
        $stmt = $conn->prepare("UPDATE `reservations` SET `finish_date`=? WHERE `id`=?");
        $stmt->bind_param("ss", $_POST['date'], $_POST["id"]);
        $stmt->execute();
    }
    else if($_POST['date'] <= $currentDate){
        $stmt = $conn->prepare("UPDATE `reservations` SET `finish_date`=?, `status`='o' WHERE `id`=?");
        $stmt->bind_param("ss", $_POST['date'], $_POST["id"]);
        $stmt->execute();
    }
    else{
        $stmt = $conn->prepare("UPDATE `reservations` SET `finish_date`=?, `status`='a' WHERE `id`=?");
        $stmt->bind_param("ss", $_POST['date'], $_POST["id"]);
        $stmt->execute();
    }  
}
?>