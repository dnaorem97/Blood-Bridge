<?php
include('./db_connection/db.php');
session_start();

// to ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// $current_page = 'donor_registration.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Management</title>
    <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/donor_reg.css">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav><?php include('./includes/header.php') ?></nav>
    <section>
        <div class="pageDesc">
            <h2>Register to Donate Blood</h2>
        </div>
        <div class="form_container">
            <form action="donor_registration.php" method="POST">
                <div class="fieldset_container"> <!-- Added wrapper for fieldsets -->
                    <fieldset class="donor_basics">
                        <legend>Basic Info</legend>
                        <!-- basic donor details -->
                        <label for="FullName">Enter Your Name:</label><br>
                        <input type="text" id="UserName" name="user_name" value="<?php echo $_SESSION['userName']; ?>" readonly><br>

                        <label for="UserEmail">Enter Your Email:</label><br>
                        <input type="email" id="UserEmail" name="user_email" value="<?php echo $_SESSION['userEmail']; ?>" readonly><br>

                        <label for="UserContact">Enter Your Contact Number:</label><br>
                        <input type="tel" id="UserContact" name="user_contact" required><br>

                        <label for="UserAge">Enter Your Age:</label><br>
                        <input type="number" id="UserAge" name="user_age" required><br><br>

                        <label for="UserGender">Select Your Gender:</label><br>
                        <input type="radio" id="male" name="user_gender" value="male" required>
                        <label for="male">Male</label>
                        <input type="radio" id="female" name="user_gender" value="female">
                        <label for="female">Female</label>
                        <input type="radio" id="other" name="user_gender" value="other">
                        <label for="other">Others</label><br><br>

                        <!-- bloodGroup -->
                        <label for="BloodType">Select Your Blood Type:</label><br>
                        <input type="radio" name="blood_group" value="A+" required>A+
                        <input type="radio" name="blood_group" value="A-">A-
                        <input type="radio" name="blood_group" value="B+">B+
                        <input type="radio" name="blood_group" value="B-">B-
                        <input type="radio" name="blood_group" value="AB+">AB+
                        <input type="radio" name="blood_group" value="AB-">AB-
                        <input type="radio" name="blood_group" value="O+">O+
                        <input type="radio" name="blood_group" value="O-">O-<br><br>

                        <label for="UserAddress">Enter Your Address:</label><br>
                        <input type="text" id="UserAddress" name="user_address" required><br>

                        <label for="lastDonationDate">Last Donation Date:</label>
                        <input type="date" id="lastDonationDate" name="last_donation_date"  required><br>

                        <label for="preferedDonationDate">Preferred Donation Date:</label>
                        <input type="date" id="preferedDonationDate" name="prefer_donation_date" required><br>

                        <label for="DonationCenter">Select Your Preferred Donation Center:</label><br>
                        <select id="DonationCenter" name="donation_center" required>
                            <option value="center1" selected>Center 1</option>
                            <option value="center2">Center 2</option>
                            <option value="center3">Center 3</option>
                        </select><br><br>

                    </fieldset>

                    <fieldset class="medical-condition">
                        <legend>Medical Conditions</legend>

                        <label for="Medications">Are you currently taking any medications?</label><br>
                        <input type="radio" name="medication" id="medicationYes" value="YES">YES
                        <input type="radio" name="medication" id="medicationYes" value="NO">NO
                        <p>if YES then mention those</p>
                        <textarea id="Medications" name="medications_textbox" rows="4" cols="50"></textarea><br>

                        <label for="ChronicDiseases">Do you have any chronic diseases (e.g., diabetes, hypertension)?</label><br>
                        <input type="checkbox" id="diabetes" name="chronic_diseases[]" value="Diabetes">
                        <label for="diabetes">Diabetes</label><br>
                        <input type="checkbox" id="hypertension" name="chronic_diseases[]" value="Hypertension">
                        <label for="hypertension">Hypertension</label><br>
                        <input type="checkbox" id="heart_disease" name="chronic_diseases[]" value="Heart Disease">
                        <label for="heart_disease">Heart Disease</label><br>
                        <input type="checkbox" id="none" name="chronic_diseases[]" value="None">
                        <label for="none">None</label><br><br>

                        <label for="Allergies">Do you have any allergies?</label><br>
                        <input type="radio" name="allergies" value="YES">YES
                        <input type="radio" name="allergies" value="NO">NO<br><br>

                        <label for="RecentSurgery">Have you had any recent surgeries?</label><br>
                        <input type="radio" name="recent_surgery" value="YES">YES
                        <input type="radio" name="recent_surgery" value="NO">NO<br>
                        <p>if yes mention here:</p>

                        <textarea id="RecentSurgery" name="recent_surgery" rows="4" cols="50"></textarea><br>

                        <label for="OtherMedicalInfo">Any other medical information you would like to provide?</label><br>
                        <textarea id="OtherMedicalInfo" name="other_medical_info" rows="4" cols="50"></textarea><br>

                        <br><button type="submit" name="submit_btn">Register as Donor</button>
                    </fieldset>
                </div> <!-- End of fieldset wrapper -->
            </form>
        </div>
    </section>

    <?php
    // include('db.php');

    if (isset($_POST['submit_btn'])) {
        if (
            !empty($_POST['user_contact']) && !empty($_POST['user_age']) && !empty($_POST['user_gender'])
            && !empty($_POST['blood_group']) && !empty($_POST['user_address'])
            && !empty($_POST['last_donation_date']) && !empty($_POST['prefer_donation_date']) && !empty($_POST['donation_center'])
        ) {
            $donor_name = $_SESSION['userName']; // Fetch from session
            $donor_email = $_SESSION['userEmail'];
            $donor_contact = $_POST['user_contact'];
            $donor_age = $_POST['user_age'];
            $donor_gender = $_POST['user_gender'];
            $donor_blood_type = $_POST['blood_group'];
            $donor_address = $_POST['user_address'];
            $donor_lastDonation_date = $_POST['last_donation_date'];
            $donor_prefered_date = $_POST['prefer_donation_date'];
            $donor_prefered_center = $_POST['donation_center'];
            $user_id = $_SESSION['user_id'];  // Fetch from session

            // Convert to DateTime objects for comparison
            $lastDonationDate = new DateTime($_POST['last_donation_date']);
            $preferDonationDate = new DateTime($_POST['prefer_donation_date']);

            // Calculate the difference in months
            $diff = $lastDonationDate->diff($preferDonationDate);
            $monthsDifference = ($diff->y * 12) + $diff->m;

            


            if ($donor_age < 18) {
                echo "<script>alert('You are not eligible to donate blood');</script>";
            } else {
                if ($monthsDifference < 3) {
                    echo "<script>alert('Thanks For Your generosity! However, you must try after  3 months. Please try later');</script>";
                } else {
                    $stmt = $conn->prepare("INSERT INTO donors(user_id, full_name, email, contact, age, gender, blood_group, address, last_donation_date,prefer_date,donation_center) 
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?)");
                    $stmt->bind_param("isssissssss", $user_id, $donor_name, $donor_email, $donor_contact, $donor_age, $donor_gender, $donor_blood_type, $donor_address, $donor_lastDonation_date, $donor_prefered_date, $donor_prefered_center);

                    if ($stmt->execute()) {
                        echo "<script>alert('Donor registered successfully!');</script>";
                        // Get the last inserted donor_id
                        $donor_id = $stmt->insert_id;

                        // Insert into donor_details table
                        $booking_date = $donor_prefered_date;
                        $time_slot = "09:00-15:00"; // Default time slot
                        $booking_stmt = $conn->prepare("INSERT INTO bookings (donor_id, donation_center, date, time_slot) 
                                             VALUES (?, ?, ?, ?)");
                        $booking_stmt->bind_param("isss", $donor_id, $donor_prefered_center, $booking_date, $time_slot);
                        if ($booking_stmt->execute()) {
                            echo "Booking confirmed successfully!";
                        } else {
                            echo "Error in booking: " . $booking_stmt->error;
                        }
                        $booking_stmt->close();
                        //end of booking insertion
                    } else {
                        echo "Error: " . $stmt->error;
                    }
                    $stmt->close();
                    $conn->close();
                }
            }
        } else {
            echo "Please fill in all required fields.";
        }
    }
    ?>


    <footer><?php include('./includes/footer.php'); ?></footer>
    <script>
        let lastDonationDate = document.getElementById()
    </script>
</body>

</html>