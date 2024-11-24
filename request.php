<?php 
include('/LPU/xampp/htdocs/Project/db_connection/db.php');
session_start();
if (!$conn) {
    die("Database connection failed:" . $conn->connect_error);
}

// Check if the user is logged in as 'admin' or 'donor'
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'donor')) {
    header("Location: login.php");
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./css/requestForm.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">
    <title>Request Blood</title>
</head>
<body>
    <nav>
        <?php include('./includes/header.php') ?>
    </nav>
    <h2 id="request-heading" ><strong>Request Blood</strong> </h2>
<body>
    <div class="request-container">
        <form action="requesthandler.php" method="post">
            <fieldset class="request-fieldset">
                <!-- <legend>Basic Info</legend> -->
                <label for="">Name:</label><br>
                <input type="text" id="UserName" name="user_name" value="<?php echo $_SESSION['userName']; ?>" readonly><br>

                <label for="UserEmail">Email:</label><br>
                <input type="email" id="UserEmail" name="user_email" value="<?php echo $_SESSION['userEmail']; ?>" readonly><br>

                <label for="UserContact">Contact Number:</label><br>
                <input type="tel" id="UserContact" name="contact" required><br>

                <label for="hospital-name">Hospital Name:</label><br>
                <input type="text" id="hospital-name" name="hospital_name" required><br><br>

                <label for="BloodType">Blood Type:</label><br>
                <input type="radio" name="blood_group_requested" value="A+" required>A+
                <input type="radio" name="blood_group_requested" value="A-">A-
                <input type="radio" name="blood_group_requested" value="B+">B+
                <input type="radio" name="blood_group_requested" value="B-">B-
                <input type="radio" name="blood_group_requested" value="AB+">AB+
                <input type="radio" name="blood_group_requested" value="AB-">AB-
                <input type="radio" name="blood_group_requested" value="O+">O+
                <input type="radio" name="blood_group_requested" value="O-">O-<br><br>

                <label for="blood-unit">Blood Units:</label>
                <input type="number" id="blood-unit" name="units_requested" required><br><br>

                <button type="submit" name="submit_btn">Submit Request</button>
            </fieldset>
        </form>
        <div class="image-container">
            <img src="./images/blood-request-01.jpg" alt="Blood Donation" />
        </div>
    </div>
</body>
</html>

        
<footer>
    <?php include('./includes/footer.php')?>
</footer>
</body>
</html>