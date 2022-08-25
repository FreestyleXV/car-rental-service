<?php
header('Content-Type: application/json');
if(isset($_GET['id'])){

    require('./db.php');
    $stmt = $conn->prepare("SELECT * FROM `reservations` WHERE `car_id`= ?");
    $stmt->bind_param("s", $_GET["id"]);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){
        $stmt->bind_result($id, $user, $car, $sdate, $fdate, $cost, $status);
        $reservations = [];
        while($stmt->fetch()){
            $reservation["id"] = $id;
            $reservation["user"] = $user;
            $reservation["car"] = $car;
            $reservation["startDate"] = $sdate;
            $reservation["finishDate"] = $fdate;
            $reservation["cost"] = $cost;
            $reservation["status"] = $status;
            array_push($reservations, $reservation);
        }
        echo json_encode($reservations);
    }
    else{
        echo json_encode([]);
    }
}
else{
    require('./db.php');
    $stmt = $conn->prepare("SELECT reservations.id, start_date, finish_date, cost, status, name, cars.id, brand, model FROM `reservations` LEFT JOIN `users` ON reservations.user_id = users.id LEFT JOIN `cars` ON reservations.car_id = cars.id");
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){
        $stmt->bind_result($id, $sdate, $fdate, $cost, $status, $user, $carId, $carBrand, $carModel);
        $reservations = [];
        while($stmt->fetch()){
            $reservation["id"] = $id;
            $reservation["user"] = $user;
            $reservation["car"] = $carId;
            $reservation["carBrand"] = $carBrand;
            $reservation["carModel"] = $carModel;
            $reservation["startDate"] = $sdate;
            $reservation["finishDate"] = $fdate;
            $reservation["cost"] = $cost;
            $reservation["status"] = $status;
            array_push($reservations, $reservation);
        }
        echo json_encode($reservations);
    }
    else{
        echo json_encode([]);
    }
}
?>