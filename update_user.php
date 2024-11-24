<?php
include('./db_connection/db.php');
session_start();

if (!$conn) {
    die("Connection Failed! " . $conn->connect_error);
}

// Check if the user is logged in and has a valid role, if not then redirect them to the login page
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'donor')) {
    echo "<script>alert('Please log in');</script>";
    header('Location: login.php');
    exit;
}
$new_userName = $new_userEmail = $new_userGender = $new_userRole = ""; // Initialize variables
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $new_userName = $_POST['name'];
    $new_userEmail = $_POST['email'];
    $new_userGender = $_POST['user_gender'];
    $new_userRole = $_POST['role'];

    // Get existing email for the current user
    $getEmail = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
    $getEmail->bind_param("i", $userId);
    $getEmail->execute();
    $queryResult = $getEmail->get_result();
    $currentEmail = $queryResult->fetch_assoc();
    $getEmail->close();

    // Compare the existing email to the new email to check if the email is changed or not
    $isEmailChanged = $new_userEmail !==  $currentEmail['email'];

    // If the email is changed, check for a duplicate email
    if ($isEmailChanged) {
        $email_duplicate_check = $conn->prepare("SELECT email FROM users WHERE email = ? AND user_id != ?");
        $email_duplicate_check->bind_param('si', $new_userEmail, $userId);
        $email_duplicate_check->execute();
        $result = $email_duplicate_check->get_result();
        $email_duplicate_check->close();

        // Check if there is a result
        if ($result->num_rows > 0) {
            //email already exist and Display alert and redirect after a short delay
            echo "<script>
                alert('Email already exists! Must be a unique email address.');
                setTimeout(function() {
                    " . ($_SESSION['role'] === 'admin' ? "window.location.href = 'testAdmin.php';" : "window.location.href = 'donor_dashboard.php';") . "
                }, 500); // Redirect after 500ms
                </script>";
            exit;
        }
    }

    //role based validation admin can everything  but donor can only edit their own profile
    // email must be unique  for all users. if Email changed upadate  it if not  then update other fields

    if ($_SESSION['role'] === 'admin') {
        // Admin can update all fields including role
        if ($isEmailChanged) {
            $updateQuery = $conn->prepare("UPDATE users SET name = ?, email = ?, gender = ?, role = ? WHERE user_id = ?");
            $updateQuery->bind_param("ssssi", $new_userName, $new_userEmail, $new_userGender, $new_userRole, $userId);
        } else {
            $updateQuery = $conn->prepare("UPDATE users SET name = ?, gender = ?, role = ? WHERE user_id = ?");
            $updateQuery->bind_param('sssi', $new_userName, $new_userGender, $new_userRole, $userId);
        }
    } else { 
        // Donor can update all fields except role
        if ($isEmailChanged) {
            $updateQuery = $conn->prepare("UPDATE users SET name = ?, email = ?, gender = ? WHERE user_id = ?");
            $updateQuery->bind_param("sssi", $new_userName, $new_userEmail, $new_userGender, $userId);
        } else {
            $updateQuery = $conn->prepare("UPDATE users SET name = ?, gender = ? WHERE user_id = ?");
            $updateQuery->bind_param('ssi', $new_userName, $new_userGender, $userId);
        }
    }
    
    if ($updateQuery->execute()) {
        // On successful update, get donor_id(s) from donors table to update those details
        echo "<script>alert('User detail updated successfully');</script>";
        
        // Fetch donor_id(s) associated with the user
        $getDonorID = $conn->prepare("SELECT donor_id FROM donors WHERE user_id = ?");
        $getDonorID->bind_param('i', $userId);
        $getDonorID->execute();
        $result = $getDonorID->get_result();
        
        if($result->num_rows > 0) {
            // Loop through each donor_id and update donor details
            while ($donorData = $result->fetch_assoc()) {
                $donorID = $donorData['donor_id'];
                
                if ($isEmailChanged) {
                    $updateDonorQuery = $conn->prepare("UPDATE donors SET full_name = ?, email = ?, gender = ? WHERE donor_id = ?");
                    $updateDonorQuery->bind_param('sssi', $new_userName, $new_userEmail, $new_userGender, $donorID);
                } else {
                    $updateDonorQuery = $conn->prepare("UPDATE donors SET full_name = ?, gender = ? WHERE donor_id = ?");
                    $updateDonorQuery->bind_param('ssi', $new_userName, $new_userGender, $donorID);
                }
                
                if($updateDonorQuery->execute()) {
                    echo "<script>alert('Donor detail updated successfully');</script>";
                } else {
                    echo "<script>alert('Error updating donor detail');</script>";
                }
            }
        }
    } else {
        echo "<script>alert('Error updating user details');</script>";
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Updated Information</title>
</head>

<body>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') : ?>
        <h1>Updated Information</h1>
        <p><strong>User ID:</strong> <?php echo htmlspecialchars($userId); ?></p>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($new_userName); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($new_userEmail); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($new_userGender); ?></p>
        <p><strong>Role:</strong> <?php echo htmlspecialchars($new_userRole); ?></p>
        
        <h2>Changes Summary</h2>
        <ul>
            <li>Email has been updated.</li>
            <li>Name has been updated.</li>
            <li>Gender has been updated.</li>
            <li>Role has been updated.</li>
        </ul>
    <?php else : ?>
        <p>No updated information available.</p>
    <?php endif; ?>
</body>

</html>

