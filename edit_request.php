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

// Initialize to avoid undefined variable notice
$request_id = '';
$requester_id = '';
$request_contact = '';
$hospital_name = '';
$blood_group = '';
$blood_units = '';
$request_status = '';
$request_date = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    // Fetch request details based on request_id
    $getRequest = $conn->prepare("SELECT * FROM requests WHERE request_id = ?");
    $getRequest->bind_param('i', $request_id);
    $getRequest->execute();
    $result = $getRequest->get_result();

    if ($requester = $result->fetch_assoc()) {
        if($requester['request_status']!= 'pending'){
            if ($_SESSION['role'] === 'admin') {
                // header("Location: testAdmin.php");
                echo "<script>alert('Request status is already approved! You cannot edit your information.');</script>";
                echo "<script>setTimeout(function(){ window.location.href = 'testAdmin.php'; }, 5);</script>";
            } elseif ($_SESSION['role'] === 'donor') {
                echo "<script>alert(''Request status is already approved! You cannot edit your information.');</script>";
                echo "<script>setTimeout(function(){ window.location.href = 'donor_dashboard.php'; }, 5);</script>";
            }
            exit;  // Make sure to exit after redirection
           
        }

        // Assign values from the fetched row
        $requester_id = $requester['request_id'];
        $request_contact = $requester['contact'];
        $hospital_name = $requester['hospital_name'];
        $blood_group = $requester['blood_group_requested'];
        $blood_units = $requester['units_requested'];
        $request_status = $requester['request_status'];
        $request_date = $requester['request_date'];
    }
    $getRequest->close();
    $current_page = 'testAdmin.php';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./css/help.css">
    <link rel="stylesheet" href="./css/edit_request.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">
    <title>Edit Request</title>
</head>

<body>
    <nav>
        <?php
        include('./includes/header.php');
        ?>
    </nav>
    <!-- <?php
            // Display request details
            echo "Hello " . htmlspecialchars($_SESSION['role']) . " - Request ID: " . htmlspecialchars($request_id) . "<br>";
            echo "Requester ID: " . htmlspecialchars($requester_id) . "<br>";
            echo "Contact: " . htmlspecialchars($request_contact) . "<br>";
            echo "Hospital Name: " . htmlspecialchars($hospital_name) . "<br>";
            echo "Blood Group: " . htmlspecialchars($blood_group) . "<br>";
            echo "Units Requested: " . htmlspecialchars($blood_units) . "<br>";
            echo "Request Status: " . htmlspecialchars($request_status) . "<br>";
            echo "Request Date: " . htmlspecialchars($request_date) . "<br>";
            ?> -->
           

    <div class="form-container">
    
    <form action="./edit_request_handler.php" method="post">
    <fieldset>
        <legend>Edit Request</legend>
        <!-- Add form fields as needed to edit request details -->
        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request_id); ?>">

        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo  $_SESSION['userName']; ?>" readonly>

        <label for="contact">Contact:</label>
        <input type="tel" name="contact" value="<?php echo $request_contact ?>">

        <label for="hospital-name">Hospital Name:</label>
        <input type="text" id="hospital-name" name="hospital_name" value="<?php echo  $hospital_name ?>">

        <!-- blood group selection -->
        <label for="requested_group">Blood Group:</label>
        <?php
        $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        foreach ($blood_groups as $group) {
            
            if ($blood_group == $group) {
                echo "<input type='radio' id='$group' name='blood_group' value='" .$group. "' checked>".$group;
            } else {
                
                echo "<input type='radio' id='$group' name='blood_group' value='". $group."' readonly>".$group;
            }
            // echo "<label for='$group'>$group</label>"; 
        }
        ?>
        <br>
        <label for="units">Blood Units:</label>
        <input type="number" value="<?php echo $blood_units;  ?>" name="blood_units" min="1" max="10" step="1">
    </fieldset>
    <div class="button-container">
        <!-- Add more form fields for editing -->
        <button type="submit" name="request_save_changes">Save Changes</button>
        <button type="button" onclick="window.location.href='<?php echo $_SESSION['role'] === 'admin' ? 'testAdmin.php' : 'donor_dashboard.php'  ?>'">Cancel</button>
    </div>
</form>

    </div>
    <section class="instant-contact">
        <?php include('./includes/help.php') ?>
    </section>

    <footer>
        <?php include('./includes/footer.php') ?>
    </footer>

</body>

</html>