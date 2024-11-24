<?php
include('./db_connection/db.php');
session_start();
if (!$conn) {
    die("Database connection failed:" . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role']) !== 'admin' &&  $_SESSION['role'] !== 'donor') {
    header("Location:login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $user_query = $conn->prepare("SELECT  * FROM users WHERE user_id=?");
    $user_query->bind_param('i', $user_id);
    $user_query->execute();
    $query_result = $user_query->get_result();

    if ($users = $query_result->fetch_assoc()) {
        $userID = $users['user_id'];
        $userName = $users['name'];
        $userEmail = $users['email'];
        $userGender = $users['gender'];
        $userRole = $users['role'];
    } else {
        echo "<script>alert('No user found!'); </script>";
    }
    $user_query->close();
    $conn->close();
    $current_page = 'testAdmin.php';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/edit_user.css">
    <link rel="stylesheet" href="./css/need_help.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Document</title>
</head>

<body>
    <nav>
        <?php include('./includes/header.php'); ?>
    </nav>
    <?php echo $user_id . " " . $userName . " " . $userEmail . " " . $userGender . " " . $userRole; ?>
    <div class="form_container">
        <h2 style="text-align: center;">Edit User Details</h2>
        <form action="update_user.php" method="POST">
            <fieldset>
                <!-- <legend>Edit User Details</legend> -->

                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                <label for="name">Name:</label>
                <input type="text" name="name" placeholder="Enter your name" value="<?php echo $userName ?>" required>

                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="Enter your email" value="<?php echo $userEmail ?>" required><br>
                <label for="UserGender">Select Your Gender:</label><br>
                <input type="radio" id="male" name="user_gender" value="male" <?php if ($userGender === 'male') echo 'checked'; ?> required>
                <label for="male">Male</label>

                <input type="radio" id="female" name="user_gender" value="female" <?php if ($userGender === 'female') echo 'checked'; ?>>
                <label for="female">Female</label>

                <input type="radio" id="other" name="user_gender" value="other" <?php if ($userGender === 'other') echo 'checked'; ?>>
                <label for="other">Others</label><br><br>
                <br>
                <!-- user role -->
                <?php if ($_SESSION['role'] === 'admin') { ?>
                    <div class="user_role">
                        <label for="role" id="role_label">Role:</label>
                        <select name="role" required>
                            <option value="donor" <?php if ($userRole === 'donor') echo 'selected'; ?>>Donor</option>
                            <option value="admin" <?php if ($userRole === 'admin') echo 'selected'; ?>>Admin</option>
                        </select>
                    </div>
                <?php } else { ?>
                    <div class="user_role">
                        <label for="role" id="role_label">Role:</label>
                        <!-- Use a hidden input for the actual form submission and a disabled input for display -->
                        <input type="hidden" name="role" value="<?php echo $userRole; ?>">
                        <input type="text" value="<?php echo ucfirst($userRole); ?>" disabled>
                    </div>
                <?php } ?>

                <div class="button-container">
                    <button type="submit" name="save_changes_btn">Save Changes</button>
                    <button type="button" onclick="window.location.href='<?php echo $_SESSION['role'] === 'admin' ? 'testAdmin.php' : 'donor_dashboard.php'; ?>'">Cancel</button>
                </div>

                <!-- <button type="submit" name="save_changes_btn">Save Changes</button> -->
                <!-- <form action="<?php echo $_SESSION['role'] === 'admin' ? 'testAdmin.php' : 'donor_dashboard' ?>" method="get">
            <button type="buttoon">Cancel</button>
        </form> -->


            </fieldset>
        </form>

    </div>
</body>

</html>