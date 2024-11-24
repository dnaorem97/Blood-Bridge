<?php
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "blood_bank_db";  

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else{
    echo "<script>console.log('Connected successfully');</script>";
}
?>
