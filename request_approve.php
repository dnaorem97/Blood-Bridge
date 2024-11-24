<?php
include('./db_connection/db.php');
session_start();

// Check connection
if (!$conn) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('location:./login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve_btn']) && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    // echo $request_id;
    $conn->begin_transaction();

    try {
        
        $get_status = $conn->prepare("SELECT blood_group_requested, units_requested, request_status FROM requests WHERE request_id = ?");
        $get_status->bind_param("i", $request_id);
        $get_status->execute();
        $result = $get_status->get_result();

        if ($reqStatus = $result->fetch_assoc()) {
            // Check if the status is pending
            if ($reqStatus['request_status'] == 'pending') {
                $requested_group = $reqStatus['blood_group_requested'];
                $requested_units = $reqStatus['units_requested'];

                $stock_check = $conn->prepare("SELECT blood_group, units_available FROM blood_stock WHERE blood_group = ?");
                $stock_check->bind_param("s", $requested_group);
                $stock_check->execute();
                $stock_result = $stock_check->get_result();

                if ($stock_status = $stock_result->fetch_assoc()) {
                    // Check if the requested units are available in the stock
                    if ($stock_status['units_available'] >= $requested_units) {
                        $update_status = $conn->prepare("UPDATE requests SET request_status = 'fulfilled' WHERE request_id = ?");
                        $update_status->bind_param("i", $request_id);
                        $update_status->execute();

                        // After approval, update the stock
                        $new_units = $stock_status['units_available'] - $requested_units;
                        $update_stock = $conn->prepare("UPDATE blood_stock SET units_available = ? WHERE blood_group = ?");
                        $update_stock->bind_param("is", $new_units, $requested_group);
                        $update_stock->execute();

                        $conn->commit();
                        echo "<script>alert('Request approved and stock updated.');</script>";
                        echo "<script>window.location.href='testAdmin.php';</script>";exit();
                    } else {
                        // Not enough stock
                        echo "<script>alert('Stock unavailable.');</script>";
                        echo "<script>window.location.href='testAdmin.php';</script>";
                        exit();
                    }
                } else {
                    // Blood group not found in stock
                    echo "<script>alert('Blood group not found in stock.');</script>";
                    echo "<script>window.location.href='testAdmin.php';</script>";
                    exit();
                }
            }else{
                echo "<script>alert('Request is already approved.');</script>";
                echo "<script>window.location.href='testAdmin.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Request not found.');</script>";
                    echo "<script>window.location.href='testAdmin.php';</script>";
                    exit();
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
// Close the database connection
$conn->close();
