<?php
include('./db_connection/db.php');
session_start();

// Check if the user is logged in as admin or donor
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'donor')) {
    header("Location: login.php");
    exit;
}

if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

if (isset($_POST['donor_id'])) {
    $donor_id = $_POST['donor_id'];

    // Check if the donation status is 'pending'
    $status_query = $conn->prepare("SELECT donation_status FROM bookings WHERE donor_id = ?");
    $status_query->bind_param('i', $donor_id);
    $status_query->execute();
    $status_result = $status_query->get_result();

    if ($donation_status = $status_result->fetch_assoc()) {
        // Check if the donation status is NOT 'pending'
        if ($donation_status['donation_status'] != 'pending') {
            // Redirect based on the user's role (admin or donor)
            if ($_SESSION['role'] === 'admin') {
                // header("Location: testAdmin.php");
                echo "<script>alert('Donation status is not pending! You cannot edit your information.');</script>";
                echo "<script>setTimeout(function(){ window.location.href = 'testAdmin.php'; }, 5);</script>";
            } elseif ($_SESSION['role'] === 'donor') {
                echo "<script>alert('Donation status is not pending! You cannot edit your information.');</script>";
                echo "<script>setTimeout(function(){ window.location.href = 'donor_dashboard.php'; }, 5);</script>";
            }
            exit;  // Make sure to exit after redirection
        } else {
            // If donation status is 'pending', fetch donor details
            $donorQuery = $conn->prepare("SELECT full_name, email, contact, age, gender, blood_group, address, last_donation_date, prefer_date, donation_center 
            FROM donors WHERE donor_id = ?");

            $donorQuery->bind_param('i', $donor_id);
            $donorQuery->execute();
            $result = $donorQuery->get_result();

            if ($donor = $result->fetch_assoc()) {
                // Populate variables with donor details
                $donor_name = htmlspecialchars($donor['full_name']);
                $donor_email = htmlspecialchars($donor['email']);
                $donor_contact = htmlspecialchars($donor['contact']);
                $donor_age = htmlspecialchars($donor['age']);
                $donor_gender = htmlspecialchars($donor['gender']);
                $donor_blood_group = htmlspecialchars($donor['blood_group']);
                $donor_address = htmlspecialchars($donor['address']);
                $donor_last_donation = htmlspecialchars($donor['last_donation_date']);
                $donor_preferred_donation = htmlspecialchars($donor['prefer_date']);
                $donor_center = htmlspecialchars($donor['donation_center']);
            } else {
                echo "<script>alert('No donor found with that ID.');</script>";
                // echo "<script>setTimeout(function(){ window.location.href = 'donor_dashboard.php'; }, 1000);</script>";
                // exit;
            }

            $donorQuery->close();
        }
    } else {
        // If no donation status found
        echo "<script>alert('Donation status is not found!');</script>";
        // echo "<script>setTimeout(function(){ window.location.href = 'donor_dashboard.php'; }, 1000);</script>";
        // exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/edit_donor.css">
    <link rel="stylesheet" href="./css/need_help.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">
    <title>Edit Donor Information</title>
</head>

<body>
    <nav>
        <?php include './includes/header.php'; ?>

    </nav>
    <?php echo  $donor_name . "" . $donor_id; ?>
    <!-- <h2>Edit Donor Information</h2> -->
    <div class="form_container">

        <form method="POST" action="update_donor.php">
            <fieldset class="donor_basics">
                <legend>Edit Donor Information</legend>
                <input type="hidden" name="donor_id" value="<?php echo  $donor_id; ?>">
                <!-- <input type="hidden" name="donation_status" value="<?php echo   $donation_status; ?>"> -->


                <!-- basic donor details -->
                <label for="FullName">Donor Name:</label><br>
                <input type="text" id="UserName" name="user_name" value="<?php echo $donor_name; ?>" readonly><br>

                <label for="UserEmail">Donor Email:</label><br>
                <input type="email" id="UserEmail" name="user_email" value="<?php echo $donor_email; ?>" readonly><br>

                <label for="UserContact">Donor Contact Number:</label><br>
                <input type="tel" id="UserContact" name="user_contact" value="<?php echo $donor_contact; ?>" required><br>

                <label for="UserAge">Donor Age:</label><br>
                <input type="number" id="UserAge" name="user_age" value="<?php echo $donor_age; ?>" required><br>

                <label for="UserGender">Gender:</label><br>
                <input type="radio" id="male" name="user_gender" value="male" <?php if ($donor_gender === 'male') echo 'checked'; ?> disabled>
                <label for="male">Male</label>
                <input type="radio" id="female" name="user_gender" value="female" <?php if ($donor_gender === 'female') echo 'checked'; ?> disabled>
                <label for="female">Female</label>
                <input type="radio" id="other" name="user_gender" value="other" <?php if ($donor_gender === 'other') echo 'checked'; ?> disabled>
                <label for="other">Others</label><br>
                <input type="hidden" name="user_gender" value="<?php echo $donor_gender; ?>">

                <!-- bloodGroup -->
                <label for="BloodType"> Blood Group :</label><br>
                <?php
                $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                foreach ($blood_groups as $group) {
                    // Check if the current group matches the donor's blood type
                    if ($donor_blood_group === $group) {
                        echo '<input type="radio" name="blood_group" value="' . $group . '" checked disabled>' . $group;
                    } else {
                        echo '<input type="radio" name="blood_group" value="' . $group . '" disabled>' . $group;
                    }
                }
                ?>
                <input type="hidden" name="blood_group" value="<?php echo $donor_blood_group; ?>"><br><br>


                <label for="UserAddress">Address:</label><br>
                <input type="text" id="UserAddress" name="user_address" value="<?php echo $donor_address; ?>" required>

                <label for="lastDonationDate">Last Donation Date:</label>
                <input type="date" id="lastDonationDate" name="last_donation_date" value="<?php echo $donor_last_donation; ?>" required>
                <br>
                <label for="preferedDonationDate">Preferred Donation Date:</label>
                <input type="date" id="preferedDonationDate" name="prefer_donation_date" value="<?php echo $donor_preferred_donation; ?>" required>
                <br>
                <label for="DonationCenter">Select Your Preferred Donation Center:</label>
                <select id="DonationCenter" name="donation_center" required>
                    <option value="center1" <?php if ($donor_center === 'center1') echo 'selected'; ?>>Center 1</option>
                    <option value="center2" <?php if ($donor_center === 'center2') echo 'selected'; ?>>Center 2</option>
                    <option value="center3" <?php if ($donor_center === 'center3') echo 'selected'; ?>>Center 3</option>
                </select>

                    <!-- Donation Status -->
                     
                     <!-- for admin -->
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <label for="donationStatus">Donation Status:</label>
                    <select name="donation_status" id="donationStatus">
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                <?php endif ?>
                <!-- For Donor: Hidden field -->
                <?php if ($_SESSION['role'] !== 'admin'): ?>
                    <input type="hidden" name="donation_status" value="<?php echo htmlspecialchars($donation_status['donation_status']); ?>">
                <?php endif; ?>

            </fieldset>
            <div class="button-container">
                <button type="submit" name="save_btn">Save Changes</button>
                <button type="button" onclick="window.location.href='<?php echo $_SESSION['role'] === 'admin' ? 'testAdmin.php' : 'donor_dashboard.php'  ?>'">Cancel</button>

            </div>
            <!-- <button type="submit" name="save_btn">Save Changes</button>
            <form action="<?php echo $_SESSION['role'] === 'admin' ? 'testAdmin.php' : 'donor_dashboard' ?>" method="get">
            <button type="submit">Cancel</button> -->
        </form>
        </form>

    </div>
    <footer><?php include('./includes/footer.php'); ?></footer>
</body>

</html>