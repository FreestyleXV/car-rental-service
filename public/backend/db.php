<?php
$conn = new mysqli("localhost", 'root', '', 'carrent');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>