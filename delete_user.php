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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user_btn'])) {
    // Get the user ID from the form
    $uid = $_POST['user_id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Step 1: Delete from bookings (if the user is a donor)
        $delete_bookings = $conn->prepare("DELETE FROM bookings WHERE donor_id IN (SELECT donor_id FROM donors WHERE user_id = ?)");
        $delete_bookings->bind_param('i', $uid);
        $delete_bookings->execute();

        // Step 2: Delete from donors
        $delete_donors = $conn->prepare("DELETE FROM donors WHERE user_id = ?");
        $delete_donors->bind_param('i', $uid);
        $delete_donors->execute();

        //step  3: Delete from request

        $delte_request = $conn->prepare("DELETE FROM requests WHERE user_id=?");
        $delte_request->bind_param('i', $uid);
        $delte_request->execute();




        // Step 4: Delete from users
        $delete_user = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $delete_user->bind_param('i', $uid);
        $delete_user->execute();

        // Commit the transaction if all queries succeeded
        $conn->commit();

        echo "<script>alert('User and related data deleted successfully!');</script>";
        echo "<script>window.location.href='testAdmin.php';</script>"; // Redirect to manage users page after deletion
    } catch (Exception $e) {
        // If any query fails, rollback the transaction
        $conn->rollback();
        echo "<script>alert('Error deleting user: " . $e->getMessage() . "');</script>";
    }

    // Close the prepared statements
    $delete_bookings->close();
    $delete_donors->close();
    $delete_user->close();
}

// Close the database connection
$conn->close();
?>
