<?php
include('./db_connection/db.php');
session_start();

// to ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Management</title>
    <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/help.css">
    <link rel="stylesheet" href="./css/leaderboard.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">

</head>

<body>
    <nav><?php include('./includes/header.php'); ?></nav>
    <!-- hero section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>Give the Gift of Life - Donate Blood Today!</h1>
            <p>Your blood donation can save up to three lives. Be the hero someone needs</p>
            <div class="hero-buttons">
                <a href="./donor_registration.php">Register Now to Donate</a>
                <a href="./blood_stock.php">Check Blood Availability</a>
                <a href="./request.php">Request Blood</a>
            </div>
            <p class="supporting-text">Every donation makes a difference. Join us in building a healthier and more compassionate community by giving the most precious gift—life itself.</p>
        </div>
    </section>

  <!-- Leaderboard Section -->
<section class="leaderboard-section">
    <h2><span><i class="fa-solid fa-trophy"></i></span> Leaderboard</h2>
    <div class="leaderboard-container">
        <table class="leaderboard-table">
            <tbody>
                <?php
                // Fetch leaderboard data from the database
                $query = "SELECT full_name, COUNT(*) as donations FROM donors GROUP BY full_name ORDER BY donations DESC LIMIT 5";
                $result = mysqli_query($conn, $query);
                $rank = 1;

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr class='leaderboard-row'>";
                    echo "<td class='rank'>";
                    if ($rank == 1) {
                        echo "<i class='fas fa-medal gold'></i>";
                    } elseif ($rank == 2) {
                        echo "<i class='fas fa-medal silver'></i>";
                    } elseif ($rank == 3) {
                        echo "<i class='fas fa-medal bronze'></i>";
                    } else {
                        echo "<span>{$rank}</span>";
                    }
                    echo "</td>";
                    echo "<td class='name'>{$row['full_name']}</td>";
                    echo "<td class='position'>";
                    if ($rank == 1) {
                        echo "1st";
                    } elseif ($rank == 2) {
                        echo "2nd";
                    } elseif ($rank == 3) {
                        echo "3rd";
                    } else {
                        echo $rank;
                    }
                    echo "</td>";
                    echo "<td class='donations'>{$row['donations']}</td>";
                    echo "</tr>";
                    $rank++;
                }
                ?>
            </tbody>
        </table>
    </div>
</section>






    <!-- gallery-section -->
    <section class="gallery-section">
        <div class="slider">
            <img src="./images/image2.jpg" alt="img1">
            <img src="./images/images3.jpg" alt="img2">
            <img src="./images/images4.jpg" alt="img3">
            <img src="./images/images5.jpg" alt="img4">
        </div>
    </section>

    <section class="instant-contact">
        <?php include('./includes/help.php') ?>
    </section>
    <footer><?php include('./includes/footer.php') ?></footer>

</body>

</html>