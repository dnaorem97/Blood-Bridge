<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">
</head>

<body>
    <h2>Login</h2>

    <div class="form_container">
        <form action="login.php" method="POST">
            <fieldset>
                <legend> <i></i>Log In Now</legend>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <p>New User? <a href="./signup.php">SIGN UP NOW</a></p>

                <button type="submit" name="login">Log In</button>
                <br>
               <p><a href="change_password.php">Forget Password ?</a></p>
            </fieldset>
        </form>

        <!-- Social Media Links -->
        <div class="social-icons">
            <a href="https://www.facebook.com" class="fab fa-facebook"></a>
            <a href="https://www.twitter.com" class="fab fa-twitter"></a>
            <a href="https://www.instagram.com" class="fab fa-instagram"></a>
        </div>
    </div>

    <?php
    // Database connection
    include('./db_connection/db.php');
    session_start();

    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Fetch user data from the database
        $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $query->bind_param('s', $email);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows == 1) {
            $users = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $users['password'])) {
                // Regenerate session ID
                session_regenerate_id(true);

                // Set session variables
                $_SESSION['user_id'] = $users['user_id'];
                $_SESSION['role'] = $users['role'];
                $_SESSION['userName'] = $users['name'];
                $_SESSION['userEmail'] = $users['email'];
                $_SESSION['gender'] = $users['gender'];

                $uid = $_SESSION['user_id'];
                // Get donor info
$donor_query = $conn->prepare("SELECT * FROM donors WHERE user_id = ?");
$donor_query->bind_param('i', $uid);
$donor_query->execute();
$donor_result = $donor_query->get_result();

if ($donor_result->num_rows === 1) {
    $donors = $donor_result->fetch_assoc();
    $_SESSION['donor_id'] = $donors['donor_id'];
    $_SESSION['full_name'] = $donors['full_name'];
    $_SESSION['contact'] = $donors['contact'];
    $_SESSION['age'] = $donors['age'];
    $_SESSION['gender'] = $donors['gender'];
    $_SESSION['blood_group'] = $donors['blood_group'];
    $_SESSION['address'] = $donors['address'];
    $_SESSION['last_donation'] = $donors['last_donation_date'];
    $_SESSION['prefer_date'] = $donors['prefer_date'];
    $_SESSION['donation_center'] = $donors['donation_center'];
}


                // Redirect based on user role
                if ($users['role'] == 'admin') {
                    header('Location: testAdmin.php');
                } else {
                    header('Location: donor_dashboard.php');
                }
                exit; 
            } else {
                echo "<script>alert('Invalid password!')</script>";
            }
        } else {
            echo "<script>alert('No user found with that email!')</script>";
        }
    }
    ?>

</body>

</html>