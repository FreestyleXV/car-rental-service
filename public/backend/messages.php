<?php
header('Content-Type: application/json');
if(isset($_POST['login'])){

    $login = $_POST['login'];

    require('./db.php');
    $stmt = $conn->prepare("SELECT `title` ,`text`, `date_sent`, `msg_read` FROM `messages` LEFT JOIN `users` ON messages.user_id = users.id WHERE `name`= ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){
        $stmt->bind_result($title, $text, $date, $read);
        $messages = [];
        while($stmt->fetch()){
            $message['title'] = $title;
            $message['text'] = $text;
            $message['date'] = $date;
            $message['read'] = $read;
            array_push($messages, $message);
        }
        echo json_encode($messages);
    }
    else{
        echo json_encode([]);
    }
    // $stmt->close();
    $stmt = $conn->prepare("UPDATE `messages` LEFT JOIN `users` ON messages.user_id = users.id SET `msg_read`= 1 WHERE `name`= ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $conn->close();
}
else{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Requset");
}
?>