<?php
// Include database connection
include('./db_connection/db.php');
session_start();


// Ensure that the connection is valid
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_done_btn'])) {
    // Get the donor ID from the form
    $donor_id = $_POST['donor_id'];
    // echo "$donor_id";

    $conn->begin_transaction();
    try {
            //get last donation and prefered donation date
            $getDate = $conn->prepare("SELECT last_donation_date, prefer_date FROM donors WHERE donor_id=?");
            $getDate->bind_param('s',$donor_id);
            $getDate->execute();
            $result = $getDate->get_result();
            $dates=$result->fetch_assoc();
            $last_donation_date = $dates['last_donation_date'];
            $prefer_date = $dates['prefer_date'];





        // Get the current donation status
        $getStatus = $conn->prepare("SELECT donation_status FROM bookings WHERE donor_id = ?");
        $getStatus->bind_param("i", $donor_id);
        $getStatus->execute();
        $getStatus->bind_result($donation_status);
        $getStatus->fetch();
        $getStatus->close();

        // Update the status of the donor in the database to 'completed'
        if ($donation_status == 'pending') {

            $statusUpdate = $conn->prepare("UPDATE bookings SET donation_status = ?,date=? WHERE donor_id = ?");
            $new_status = 'completed'; // Change this as needed
            $statusUpdate->bind_param('ssi', $new_status,$last_donation_date, $donor_id);
            $statusUpdate->execute();
            $statusUpdate->close();

            // Get blood group from bookings
            $getBloodGroup = $conn->prepare("SELECT blood_group FROM donors WHERE donor_id = ?");
            $getBloodGroup->bind_param("i", $donor_id);
            $getBloodGroup->execute();
            $getBloodGroup->bind_result($blood_group);
            $getBloodGroup->fetch();
            $getBloodGroup->close();

            // Insert or update blood stock using ON DUPLICATE KEY UPDATE
            if ($blood_group) { // Ensure blood group was found
                
                $update_stock_query = $conn->prepare("INSERT INTO blood_stock (blood_group, units_available, last_updated) 
                    VALUES (?, 1, NOW()) 
                    ON DUPLICATE KEY UPDATE  units_available = units_available + 1,  last_updated = NOW()");
                $update_stock_query->bind_param("s", $blood_group);
                $update_stock_query->execute();
                $update_stock_query->close();

                $update_donation_date = $conn->prepare("UPDATE donors SET last_donation_date = ? WHERE donor_id =?");
                $update_donation_date->bind_param('si', $prefer_date, $donor_id);
                $update_donation_date->execute();
                $update_donation_date->close();
               

               if ($conn->commit()){
                echo "<script>alert('Donation marked as completed successfully.')</script>";
                echo "<script>window.location.href='testAdmin.php';</script>";
                // header('Location: testAdmin.php');
               } 
            } else {
                throw new Exception("Blood group not found for donor ID: $donor_id");
            }
        } else {
            echo "<script>alert('The current donation status is not pending.')</script>";
            echo "<script>window.location.href='testAdmin.php';</script>";
            // header('Location: testAdmin.php');
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Transaction failed: " . $e->getMessage();
    }
}

// Close the database connection
$conn->close();
?>