<?php
$host = "localhost";
$user = "213583";
$password = "enkA2I2xkffL1kTq";
$database="213583";
$conn = new mysqli($host, $user, $password, $database);

if($conn->connect_error) {
    die("Yhteys epäonnistui:" . $conn->connect_error);
}

?>