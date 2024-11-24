<?php
include('./db_connection/db.php');

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
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">
</head>

<body>
    <nav class="index-nav">
        <ul>
            <li><h2>Blood Bank Management</h2></li>
            <li><a href="login.php" style="background-color: white; color:black; padding: 10px 15px;
    border-radius: 4px;"> LogIn</a></li>
        </ul>
    </nav>
    <!-- hero section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>Give the Gift of LIfe - Donate Blood Today!</h1>
            <p>Your blood donation can save up to three life. Be the hero someone needs</p>
            <div class="hero-buttons">
                <a href="./donor_registration.php">Register Now to Donate</a>
                <a href="./blood_stock.php">Check Blood Availibility </a>
                <a href="./request.php">Request Blood</a>

            </div>
            <p class="supporting-text">Every donation makes a difference.
                Join us in building a healthier and more compassionate community by giving the most precious gift—life itself.</p>
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
        <?php include('./includes/help.php')?>
    </section>
    <footer><?php include('./includes/footer.php') ?></footer>

</body>

</html>