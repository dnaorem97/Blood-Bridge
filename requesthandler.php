<?php 
include('./db_connection/db.php');
session_start();

if(!$conn){
    die("Connection Failed:". $conn->connect_error);
}
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION['user_id'])){
    header('Location:login.php');
}
$current_page = basename($_SERVER['PHP_SELF']);

if(($_SERVER['REQUEST_METHOD']=='POST') &&  (isset($_POST['submit_btn']))){
    $user_id = $_SESSION['user_id'];
    $requester_name = $_POST['user_name'];
    $requester_email = $_POST['user_email'];
    $requester_contact = $_POST['contact'];
    $hospital_name = $_POST['hospital_name'];
    $blood_type = $_POST['blood_group_requested'];
    $unit_requested = $_POST['units_requested'];

    $request_query = $conn->prepare("INSERT INTO requests (user_id,contact,hospital_name,blood_group_requested,units_requested) 
    VALUES (?,?,?,?,?)");
    $request_query->bind_param('isssi',$user_id,$requester_contact,$hospital_name,$blood_type,$unit_requested);
    if($request_query->execute()){
        echo "<script>alert('Request Sent Successfully')</script>";
        if($_SESSION['role']==='admin'){
            echo "<script>window.location.href='testAdmin.php';</script>";
        }
        else{
            echo "<script>window.location.href='donor_dashboard.php';</script>";
        }
       
    }

}








?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request handler</title>
</head>
<body>
    <?php
     echo $_SESSION['user_id']. " ". $_SESSION['role'];
    echo $requester_name ." ". $requester_email." ". $requester_contact." ". $hospital_name." ". $blood_type." ". $unit_requested
    ?>
</body>
</html>