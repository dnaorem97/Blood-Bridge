<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/change_password.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Blood Bank Management System</title>
</head>

<body>
<nav class="index-nav">
        <ul>
            <li><h2>Blood Bank Management</h2></li>
            <li><a href="login.php" style="background-color: white; color:black; padding: 10px 15px;
    border-radius: 4px;"> LogIn</a></li>
        </ul>
    </nav>
    <H2>CHANGE PASSWORD</H2>
    <div class="changPaswd-form-conatiner">
        <form action="<?php echo ($_SERVER['PHP_SELF']) ?>" method="post">
            <fieldset>
                <legend>Change Password</legend>
                <label for="user-email">Email:</label>
                <input type="email" name="user_email" required>
                <label for="new-password">New Password:</label>
                <input type="password" name="new_password" id="new-password">
                <label for="confirm-password">Confirm your password:</label>
                <input type="password" name="confirm_password" id="confirm-password" required>
                <div class="button-container">
                <button type="submit" name="change_pass_btn">Change password</button>
                <button onclick="window.location.href='login.php'">Cancel</button>
                </div>
               
            </fieldset>
        </form>
    </div>
    <?php
    include('./db_connection/db.php');
    session_start();

    if (!$conn) {
        die('Connection failed: ' . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_pass_btn'])) {
        $email = $_POST['user_email'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password']; // Correctly retrieve confirm password

        if ($new_password !== $confirm_password) {
            echo "<script>alert('Passwords do not match.');</script>";
        } else {
            $get_user_info = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
            $get_user_info->bind_param('s', $email);
            $get_user_info->execute();
            $result = $get_user_info->get_result();

            if ($result->num_rows == 1) {
                $user_details = $result->fetch_assoc();
                $user_id = $user_details['user_id'];
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Hash the new password

                $update_password = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $update_password->bind_param('si', $hashed_password, $user_id);
                if ($update_password->execute()) {
                    echo "<script>alert('Password updated successfully.');</script>";
                } else {
                    echo "<script>alert('Error updating password. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('Email not found.');</script>";
            }
        }
    }
    ?>

</body>

</html>