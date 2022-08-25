<?php
require('./db.php');
$stmt = $conn->prepare("SELECT id, name, mail, permissions FROM `users`");
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows>0){
    $stmt->bind_result($id, $name, $mail, $permissions);
    $users = [];
    while($stmt->fetch()){
        $user["id"] = $id;
        $user["name"] = $name;
        $user["mail"] = $mail;
        $user["permissions"] = $permissions;
        array_push($users, $user);
    }
    echo json_encode($users);
}
else{
    echo json_encode([]);
}
?>