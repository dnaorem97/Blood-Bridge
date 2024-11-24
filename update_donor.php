<?php
session_start();

// Include database connection
include('./db_connection/db.php');

// Check if the user is logged in as admin or donor
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'donor' && $_SESSION['role'] !== 'admin')) {
    header("Location: login.php");
    exit;
}

// to ensure that the connection is valid
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_btn'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_POST['user_name'];
    $email = $_POST['user_email'];
    $donor_id = $_POST['donor_id'];
    $contact = $_POST['user_contact'];
    $age = $_POST['user_age'];
    $last_donation = $_POST['last_donation_date'];
    $prefer_donation_date = $_POST['prefer_donation_date']; 
    $donation_center = $_POST['donation_center'];
    $address = $_POST['user_address'];
    $donation_status=$_POST['donation_status'];

    if (
        !empty($donor_id) && !empty($username) && !empty($email)
        && !empty($contact) && !empty($age) && !empty($last_donation)
        && !empty($prefer_donation_date) && !empty($donation_center) && !empty($address)
    ) {
        $conn->begin_transaction();
        try {
            // Update donor information
            $donor_update_query = $conn->prepare("UPDATE donors SET contact=?, age=?, address=?, last_donation_date=?, donation_center=?, prefer_date=? WHERE donor_id=?");
            $donor_update_query->bind_param('sissssi', $contact, $age, $address, $last_donation, $donation_center, $prefer_donation_date, $donor_id);
            
            if (!$donor_update_query->execute()) {
                throw new Exception("Failed to update donor info: " . $donor_update_query->error);
            }

            // Update booking date
            $update_booking_date_query = $conn->prepare("UPDATE bookings SET date=?,donation_center=? WHERE donor_id=?");
            $update_booking_date_query->bind_param('ssi', $prefer_donation_date, $donation_center,$donor_id);
            if (!$update_booking_date_query->execute()) {
                throw new Exception("Failed to update booking date: " . $update_booking_date_query->error);
            }

            $conn->commit();
            echo "<script>alert('Donor and booking information updated successfully!');</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        } finally {
            $donor_update_query->close();
            $update_booking_date_query->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<!-- <body>
    <?php
    // For debugging purposes, but make sure to sanitize before echoing in production
    echo htmlspecialchars($username) . " " . htmlspecialchars($email) . " " . htmlspecialchars($user_id) . " " . htmlspecialchars($donor_id) . " " . htmlspecialchars($contact) . " " . htmlspecialchars($age) . " " . htmlspecialchars($prefer_donation_date) . " " . htmlspecialchars($donation_center) . " " . htmlspecialchars($address)." ".$donation_status;
    ?>
</body> -->
<body>
    <h1>Donor Information Updated Successfully!</h1>
    <h2>Updated Donor Details:</h2>
    <ul>
        <li><strong>Donor ID:</strong> <?php echo htmlspecialchars($donor_id); ?></li>
        <li><strong>Full Name:</strong> <?php echo htmlspecialchars($username); ?></li>
        <li><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></li>
        <li><strong>Contact:</strong> <?php echo htmlspecialchars($contact); ?></li>
        <li><strong>Age:</strong> <?php echo htmlspecialchars($age); ?></li>
        <li><strong>Last Donation Date:</strong> <?php echo htmlspecialchars($last_donation); ?></li>
        <li><strong>Preferred Donation Date:</strong> <?php echo htmlspecialchars($prefer_donation_date); ?></li>
        <li><strong>Donation Center:</strong> <?php echo htmlspecialchars($donation_center); ?></li>
        <li><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></li>
        <li><strong>Status:</strong><?php echo htmlspecialchars($donation_status)?></li>
    </ul>
    <h1>BAck to Home
        <a href="index.php">Home</a>
    </h1>
</body>

</html>
