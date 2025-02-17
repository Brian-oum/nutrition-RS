<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "nutrition";

$conn = new mysqli($host, $email, $password, $database);

if ($conn->connect_error){
    die("Connection failed" .$conn->connect_error);
}
?>