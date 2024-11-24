<?php
session_start();

// Include database connection
include('./db_connection/db.php');

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ensure that the connection is valid
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error());
}

//donation
$totalUsersQuery = "SELECT COUNT(*) as total FROM users";
$totalDonorsQuery = "SELECT COUNT(*) as total FROM donors";
$totalBloodQuery = "SELECT COALESCE(SUM(units_available), 0) as total FROM blood_stock";
$pendingDonationQuery = "SELECT COUNT(*) as total FROM bookings WHERE donation_status = 'pending'";
$completedDonationQuery = "SELECT COUNT(*) as total FROM bookings WHERE donation_status = 'completed'";

$totalUsersResult = $conn->query($totalUsersQuery);
$totalDonorsResult = $conn->query($totalDonorsQuery);
$totalBloodResult = $conn->query($totalBloodQuery);
$pendingDonationResult = $conn->query($pendingDonationQuery);
$completedDonationResult = $conn->query($completedDonationQuery);


// Error handling for query execution
if (!$totalUsersResult || !$totalDonorsResult || !$totalBloodResult || !$pendingDonationResult || !$completedDonationResult) {
    die("Query failed: " . $conn->error);
}
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['total'];
$totalDonors = mysqli_fetch_assoc($totalDonorsResult)['total'];
$totalBlood = mysqli_fetch_assoc($totalBloodResult)['total'];
$pendingDonation = mysqli_fetch_assoc($pendingDonationResult)['total'];
$completedDonation = mysqli_fetch_assoc($completedDonationResult)['total'];


// Requests
$totalRequestsQuery = "SELECT COUNT(*) as total FROM requests";
$pendingRequestsQuery = "SELECT COUNT(*) as total FROM requests WHERE request_status = 'pending'";
$fulfilledRequestsQuery = "SELECT COUNT(*) as total FROM requests WHERE request_status = 'fulfilled'";

$totalRequestsResult = $conn->query($totalRequestsQuery);
$pendingRequestsResult = $conn->query($pendingRequestsQuery);
$fulfilledRequestsResult = $conn->query($fulfilledRequestsQuery);

$totalRequests = mysqli_fetch_assoc($totalRequestsResult)['total'];
$pendingRequests = mysqli_fetch_assoc($pendingRequestsResult)['total'];
$fulfilledRequests = mysqli_fetch_assoc($fulfilledRequestsResult)['total'];


// to highlight the active a
// $current_page = 'testAdmin.php';
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
    <link rel="stylesheet" href="./css/admin_dashboard.css">
    <link rel="stylesheet" href="./css/need_help.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <nav><?php
            include('./includes/header.php');
            ?></nav>


    <div class="dashboard-container">
        <div class="panel-container">
            <div class="panel total-users">
                <h3>Total Users</h3>
                <p><?php echo $totalUsers; ?></p> <!-- Dynamic number from database -->
            </div>
            <div class="panel total-donors">
                <h3>Total Donors</h3>
                <p><?php echo $totalDonors; ?></p> <!-- Dynamic number from database -->
            </div>

            <div class="panel total-blood-units">
                <h3>Total Blood Units</h3>
                <p><?php echo $totalBlood; ?></p> <!-- Dynamic number from database -->
            </div>

            <div class="panel donation">
                <h3>Donation Stats</h3>
                <p>Pending: <?php echo $pendingDonation ?></p>
                <p>Completed: <?php echo $completedDonation; ?></p>
            </div>
            <div class="panel request">
                <h3>Request Stats</h3>
                <p>Pending: <?php echo $pendingRequests; ?></p>
                <p>Fulfilled: <?php echo $fulfilledRequests; ?></p>
            </div>
        </div>


        <!-- User Management Section -->
<section class="user-management-table">
    <h2>User Information</h2>
    <table border="1">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $user_query = "SELECT user_id, name, email FROM users";
            $user_result = $conn->query($user_query);
            $row_count = 0;
            while ($user_detail = $user_result->fetch_assoc()):
                $row_count++;
                $row_class = $row_count > 5 ? 'hidden-row' : '';
            ?>
                <tr class="<?php echo $row_class; ?>">
                    <td><?php echo $user_detail['user_id']; ?></td>
                    <td><?php echo $user_detail['name']; ?></td>
                    <td><?php echo $user_detail['email']; ?></td>
                    <td>
                        <form method="POST" action="./edit_user.php" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user_detail['user_id']; ?>">
                            <button type="submit" name="edit_user_btn">Edit</button>
                        </form>
                        <form method="POST" action="./delete_user.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="user_id" value="<?php echo $user_detail['user_id']; ?>">
                            <button type="submit" name="delete_user_btn" class="delBtn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <span class="button-wrapper" style="float: right;">
    <button id="user-expand-btn" onclick="toggleUserRows()">Show More</button>
    </span>
</section>

<!-- Donor Management Section -->
<section class="donor-management-table">
    <h2>Donor Information</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Blood Group</th>
                <th>Contact</th>
                <th>Donation Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $donor_query = "SELECT donors.donor_id, donors.full_name, donors.blood_group, donors.contact, donors.last_donation_date, bookings.donation_status 
                            FROM donors 
                            LEFT JOIN bookings ON donors.donor_id = bookings.donor_id";
            $donor_result = $conn->query($donor_query);
            $row_count = 0;
            while ($donor_detail = $donor_result->fetch_assoc()):
                $row_count++;
                $row_class = $row_count > 5 ? 'hidden-row' : '';
            ?>
                <tr class="<?php echo $row_class; ?>">
                    <td><?php echo $donor_detail['full_name']; ?></td>
                    <td><?php echo $donor_detail['blood_group']; ?></td>
                    <td><?php echo $donor_detail['contact']; ?></td>
                    <td><?php echo $donor_detail['last_donation_date']; ?></td>
                    <td><?php echo $donor_detail['donation_status']; ?></td>
                    <td>
                        <form method="POST" action="edit_donor.php" style="display:inline;">
                            <input type="hidden" name="donor_id" value="<?php echo $donor_detail['donor_id']; ?>">
                            <button type="submit" id="edit-btn" name="edit_donor_btn">Edit</button>
                        </form>
                        <form method="POST" action="delete_donor.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this Donor?');">
                            <input type="hidden" name="donor_id" value="<?php echo $donor_detail['donor_id']; ?>">
                            <button type="submit" name="delete_donor_btn" class="delBtn">Delete</button>
                        </form>
                        <form method="POST" action="mark_done.php" style="display:inline;">
                            <input type="hidden" name="donor_id" value="<?php echo $donor_detail['donor_id']; ?>">
                            <button type="submit" id="mark-btn" name="mark_done_btn">Mark as Done</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <span class="button-wrapper" style="float: right;">
    <button id="donor-expand-btn" onclick="toggleDonorRows()">Show More</button>
    </span>
</section>

<!-- Request Management Section -->
<section class="requester-management-table">
    <h2>Request Information</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Req ID</th>
                <th>Name</th>
                <th>Blood Group</th>
                <th>Contact</th>
                <th>Req Units</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $request_query = "SELECT requests.*, users.name 
                              FROM requests 
                              JOIN users ON requests.user_id = users.user_id";
            $request_result = $conn->query($request_query);
            $row_count = 0;
            while ($requester = $request_result->fetch_assoc()):
                $row_count++;
                $row_class = $row_count > 5 ? 'hidden-row' : '';
            ?>
                <tr class="<?php echo $row_class; ?>">
                    <td><?php echo $requester['request_id']; ?></td>
                    <td><?php echo $requester['name']; ?></td>
                    <td><?php echo $requester['blood_group_requested']; ?></td>
                    <td><?php echo $requester['contact']; ?></td>
                    <td><?php echo $requester['units_requested']; ?></td>
                    <td><?php echo $requester['request_status']; ?></td>
                    <td>
                        <form method="POST" action="./edit_request.php" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo $requester['request_id']; ?>">
                            <button type="submit" id="edit-request-btn" name="edit_request_btn">Edit</button>
                        </form>
                        <form method="POST" action="./delete_request.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this request?');">
                            <input type="hidden" name="request_id" value="<?php echo $requester['request_id']; ?>">
                            <button type="submit" name="delete_req_btn" class="delBtn">Delete</button>
                        </form>
                        <form method="POST" action="./request_approve.php" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo $requester['request_id']; ?>">
                            <button type="submit" id="approve-btn" name="approve_btn">Approve</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <span class="button-wrapper" style="float: right;">

    <button id="request-expand-btn" onclick="toggleRequestRows()">Show More</button>
    </span>
</section>

<!-- JavaScript and CSS for the expand/collapse functionality -->
<script>
    function toggleUserRows() {
        const rows = document.querySelectorAll('.user-management-table .hidden-row');
        const button = document.getElementById('user-expand-btn');
        rows.forEach(row => row.style.display = row.style.display === 'table-row' ? 'none' : 'table-row');
        button.textContent = button.textContent === 'Show More' ? 'Show Less' : 'Show More';
    }

    function toggleDonorRows() {
        const rows = document.querySelectorAll('.donor-management-table .hidden-row');
        const button = document.getElementById('donor-expand-btn');
        rows.forEach(row => row.style.display = row.style.display === 'table-row' ? 'none' : 'table-row');
        button.textContent = button.textContent === 'Show More' ? 'Show Less' : 'Show More';
    }

    function toggleRequestRows() {
        const rows = document.querySelectorAll('.requester-management-table .hidden-row');
        const button = document.getElementById('request-expand-btn');
        rows.forEach(row => row.style.display = row.style.display === 'table-row' ? 'none' : 'table-row');
        button.textContent = button.textContent === 'Show More' ? 'Show Less' : 'Show More';
    }
</script>

<style>
    .hidden-row {
        display: none;
    }
    .user-expand-btn,.donor-expand-btn,.request-expand-btn {
        margin-top: 10px;
        padding: 8px 16px;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
</style>

    </div>
    <footer><?php include('./includes/footer.php'); ?></footer>


    <!-- <script>
        window.onload = function() {
            let editBtn = document.getElementById("edit-btn");
            let markBtn = document.getElementById("mark-btn");
            let donationStat = document.getElementById("donation-status");

            let status = donationStat.innerText.trim();
            markBtn.addEventListener('click', function() {
                // Disable buttons if status is "completed"
                if (status === "completed") {
                    editBtn.disabled = true;
                    markBtn.disabled = true;
                    editBtn.style.backgroundColor = "red";
                    markBtn.style.backgroundColor = "red";
                }
            })


        }
    </script> -->
</body>

</html>