<?php
session_start();

// Include database connection
include('db.php');

// Check if the user is logged in as admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit;
}

// Ensure that the connection is valid
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch total donors, blood units, and pending/completed bookings
$totalDonorsQuery = "SELECT COUNT(*) as total FROM donors";
$totalBloodQuery = "SELECT COALESCE(SUM(units_available), 0) as total FROM blood_stock";
$pendingDonationQuery = "SELECT COUNT(*) as total FROM bookings WHERE donation_status = 'pending'";
$completedDonationQuery = "SELECT COUNT(*) as total FROM bookings WHERE donation_status = 'completed'";

$totalDonorsResult = $conn->query($totalDonorsQuery);
$totalBloodResult = $conn->query($totalBloodQuery);
$pendingDonationResult = $conn->query($pendingDonationQuery);
$completedDonationResult = $conn->query($completedDonationQuery);

// Error handling for query execution
if (!$totalDonorsResult || !$totalBloodResult || !$pendingDonationResult || !$completedDonationResult) {
    die("Query failed: " . $conn->error);
}

$totalDonors = mysqli_fetch_assoc($totalDonorsResult)['total'];
$totalBlood = mysqli_fetch_assoc($totalBloodResult)['total'];
$pendingDonation = mysqli_fetch_assoc($pendingDonationResult)['total'];
$completedDonation = mysqli_fetch_assoc($completedDonationResult)['total'];

// Fetch recent admin activity (if you track it)
// $recentActivityQuery = "SELECT action_details FROM admin_actions ORDER BY action_timestamp DESC LIMIT 5";
// $recentActivityResult = mysqli_query($conn, $recentActivityQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Dashboard</title>
    <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/admin_dashboard.css">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
<nav><?php include('./includes/header.php') ?></nav>

<!-- Dashboard Panels -->
<div class="dashboard-container">
    <div class="panel total-donors">
        <h3>Total Donors</h3>
        <p><?php echo $totalDonors; ?></p> <!-- Dynamic number from database -->
    </div>

    <div class="panel total-blood-units">
        <h3>Total Blood Units</h3>
        <p><?php echo $totalBlood; ?></p> <!-- Dynamic number from database -->
    </div>

    <div class="panel pending-donation">
        <h3>Pending Donation</h3>
        <p><?php echo $pendingDonation; ?></p>
    </div>
    <div class="panel completed-donation">
        <h3>completed Donation</h3>
        <p><?php echo $pendingDonation; ?></p>
    </div>

    <div class="panel recent-activity">
        <h3>Recent Activity</h3>
        <ul>
            <?php while($activity = mysqli_fetch_assoc($recentActivityResult)): ?>
                <li><?php echo $activity['action_details']; ?></li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>

<!-- Table Sections -->
<section class="table-section">
    <h2>Donors Management</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Blood Group</th>
                <th>Contact</th>
                <th>Last Donation Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
            // Updated query with JOIN
            $donorsQuery = "SELECT d.full_name, d.blood_group, d.contact, d.last_donation_date
                            FROM donors d
                            JOIN users u ON d.user_id = u.user_id";
            $donorsResult = mysqli_query($conn, $donorsQuery);
            
            // Fetch and display each donor row
            while($donor = mysqli_fetch_assoc($donorsResult)): ?>
                <tr>
                    <td><?php echo $donor['full_name']; ?></td>
                    <td><?php echo $donor['blood_group']; ?></td>
                    <td><?php echo $donor['contact']; ?></td>
                    <td><?php echo $donor['last_donation_date']; ?></td>
                    <td>
                        <button>Edit</button>
                        <button>Delete</button>
                        <button>Mark as completed</button>
                        
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

<section class="table-section">
    <h2>Blood Stock Management</h2>
    <table>
        <thead>
            <tr>
                <th>Blood Group</th>
                <th>Available Units</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $bloodStockQuery = "SELECT blood_group, units_available FROM blood_stock";
            $bloodStockResult = mysqli_query($conn, $bloodStockQuery);
            while($stock = mysqli_fetch_assoc($bloodStockResult)): ?>
                <tr>
                    <td><?php echo $stock['blood_group']; ?></td>
                    <td><?php echo $stock['units_available']; ?></td>
                    <td><button>Update Stock</button></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

</body>
</html>
