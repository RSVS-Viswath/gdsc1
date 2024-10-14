<?php
$servername = "sql205.infinityfree.com"; 
$username = "if0_34689760";
$password = "qhgFpPPYSG4";
$dbname = "if0_34689760_gdsc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
