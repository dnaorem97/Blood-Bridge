<?php 
include('./db_connection/db.php');
session_start();

if(!$conn){
    die("Connection Failed!:".$conn->conncect_error);
}



if(!isset($_SESSION['user_id']) && $_SESSION['role']!=='admin'){
    header('location:login.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_req_btn'])){
   
    $req_id = $_POST['request_id'];
    echo  "delete button clicked <br>";
    echo  $req_id;

    $del_req_query = $conn->prepare("DELETE FROM  requests WHERE request_id = ?");
    $del_req_query->bind_param('i',$req_id);
    if( $del_req_query->execute()){
        echo "<script>alert('Request deleted successfully');</script>";
        echo "<script>window.location.href='testAdmin.php'</script>";
        exit();
    }
   
    $del_req_query->close();
}

$conn->close();
?>