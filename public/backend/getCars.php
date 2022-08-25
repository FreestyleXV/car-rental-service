<?php
header('Content-Type: application/json');

require('./db.php');
$stmt = $conn->prepare("SELECT * FROM `cars`");
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows>0){
    $stmt->bind_result($id, $brand, $model, $year, $price, $available);
    $cars = [];
    while($stmt->fetch()){
        $car["id"] = $id;
        $car["brand"] = $brand;
        $car["model"] = $model;
        $car["year"] = $year;
        $car["price"] = $price;
        if($available==1){
            $car["available"] = true;
        }
        else{
            $car["available"] = false;
        }
        array_push($cars, $car);
    }
    echo json_encode($cars);
}
else{
    echo json_encode([]);
}
?>