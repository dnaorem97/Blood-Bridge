<?php
session_start();

// Include database connection
include('./db_connection/db.php');

// Check if the user is logged in or not and checking if role is neither admin or donor
// if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'donor' && $_SESSION['role'] !== 'admin')) {
//     header("Location: login.php");
//     exit;
// }

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


// Ensure that the connection is valid
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error());
}
$totalDonorsQuery = "SELECT COUNT(*) as total FROM donors";
$totalRequestsQuery = "SELECT COUNT(*) as total FROM requests";
$totalBloodQuery = "SELECT COALESCE(SUM(units_available), 0) as total FROM blood_stock";


$totalDonorsResult = $conn->query($totalDonorsQuery);
$totalRequestsResult = $conn->query($totalRequestsQuery);
$totalBloodResult = $conn->query($totalBloodQuery);


// Error handling for query execution
if (!$totalDonorsResult || !$totalBloodResult || !$totalRequestsResult) {
    die("Query failed: " . $conn->error);
}
$totalDonors = mysqli_fetch_assoc($totalDonorsResult)['total'];
$totalRequest = mysqli_fetch_assoc($totalRequestsResult)['total'];
$totalBlood = mysqli_fetch_assoc($totalBloodResult)['total'];


//center wise  blood availability
$centerwiseBloodQuery = "
    SELECT donors.donation_center, SUM(blood_stock.units_available) as total_units 
    FROM blood_stock 
    JOIN donors ON blood_stock.blood_group = donors.blood_group
    JOIN bookings ON donors.donor_id = bookings.donor_id 
    WHERE bookings.donation_status = 'completed'
    GROUP BY donors.donation_center
";
$centerwiseBloodResult = $conn->query($centerwiseBloodQuery);



// $current_page = 'blood_stock.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Management</title>
    <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/stocks.css">
    <link rel="stylesheet" href="./css/need_help.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <nav>
        <?php
        include('./includes/header.php');
        ?>
    </nav>
    <div class="stock-dashboard-container">
        <div class="panel-container">
            <div class="panel total-donors">
                <h3>Total Donors</h3>
                <p><?php echo $totalDonors; ?></p> <!-- Dynamic number from database -->
            </div>
            <div class="panel total-donors">
                <h3>Total Requests</h3>
                <p><?php echo $totalRequest; ?></p> <!-- Dynamic number from database -->
            </div>

            <div class="panel total-blood-units">
                <h3>Total Blood Units</h3>
                <p><?php echo $totalBlood; ?></p> <!-- Dynamic number from database -->
            </div>
        </div>
        <span id="refresh-btn">
    <button type="button" name="refreshBtn" onclick="location.reload();">Refresh</button>
</span>


    </div>


    <!-- stock info section -->
    <section class="table-section blood-stock-table ">
        <h2>Stock Information</h2>
        <div class="table-container">
            <table>
                <caption>All Center</caption>
                <thead>
                    <tr>
                        <th>Blood Groups</th>
                        <th>Units Available</th>
                        <th>Last Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query to fetch all records from blood_stock table ordered by blood_group
                    $stocks_query = "SELECT * FROM blood_stock ORDER BY blood_group";
                    $result = $conn->query($stocks_query); // Execute query

                    if ($result->num_rows > 0) {
                        // Loop through the results and display each row
                        while ($stock_record = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $stock_record['blood_group']; ?></td>
                                <td><?php echo $stock_record['units_available']; ?></td>
                                <td><?php echo $stock_record['last_updated'] ?></td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='3'>No stock data available.</td></tr>";
                    }

                    $result->close(); // Close the result set
                    ?>
                </tbody>

            </table>
    </section>



    <!-- table section -->
    <section class="table-section donor-info-table">
        <h2>Donor Information</h2>
        <table>
            <thead>
                <tr>
                    <th>Donor ID</th>
                    <th>Name</th>
                    <th>Blood Group</th>
                    <th>Contact</th>
                    <th>Last Donation Date</th>
                    <th>Status</th>
                    <!-- <th>Actions</th> -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch donor details
                $donor_query = "SELECT donors.donor_id, donors.full_name, donors.blood_group, donors.contact, donors.last_donation_date, bookings.donation_status 
                            FROM donors LEFT JOIN bookings ON donors.donor_id = bookings.donor_id WHERE  bookings.donation_status = 'completed'";


                $donor_result = $conn->query($donor_query);

                while ($donor_detail = $donor_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $donor_detail['donor_id']; ?></td>
                        <td><?php echo $donor_detail['full_name']; ?></td>
                        <td><?php echo $donor_detail['blood_group']; ?></td>
                        <td><?php echo $donor_detail['contact']; ?></td>
                        <td><?php echo $donor_detail['last_donation_date']; ?></td>
                        <td><?php echo $donor_detail['donation_status']; ?></td>
                        <!-- <td>
                                <form method="POST" action="edit_donor.php" style="display:inline;">
                                    <input type="hidden" name="donor_id" value="<?php echo $donor_detail['donor_id']; ?>">
                                    <button type="submit" id="edit-btn" name="edit_donor_btn">Edit</button>
                                </form>
                                <form method="POST" action="delete_donor.php" style="display:inline;">
                                    <input type="hidden" name="donor_id" value="<?php echo $donor_detail['donor_id']; ?>">
                                    <button type="submit" name="delete_donor_btn">Delete</button>
                                </form>
                                <form method="POST" action="mark_done.php" style="display:inline;">
                                    <input type="hidden" name="donor_id" value="<?php echo $donor_detail['donor_id']; ?>">
                                    <button type="submit" id="mark-btn" name="mark_done_btn">Mark as Done</button>
                                </form>
                            </td> -->
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
    </div>
    <footer><?php include('./includes/footer.php') ?></footer>



</body>

</html>