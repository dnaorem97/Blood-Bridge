<?php
session_start();

// to ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blood Bank Management</title>
  <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/styles.css">
  <link rel="stylesheet" href="./css/footer.css">
  <link rel="stylesheet" href="./css/contact_us.css">
  <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
  <header>
    <nav><?php include('./includes/header.php'); ?></nav>
  </header>

  <section id="contact" class="contactSection">
    <!-- <h1>Contact Me</h1> -->
    <h2>Have a Question? Contact Us</h2>

    <p class="contact-description">We'd love to hear from you! Whether you have a question,
      need assistance, or want to share your feedback, our friendly team is here to help.</p>
    <div class="contact-me-container">
      <div class="contactme-item">
        <i class="fa-solid fa-map-location-dot"></i>
        <div>
          <h3>Address</h3>
          <p>1234 Phagwara, Punjab, India</p>
        </div>
      </div>
      <div class="contactme-item">
        <i class="fa-solid fa-phone"></i>
        <div>
          <h3>Call Us</h3>
          <p>Phone: 123-456-7890</p>
        </div>
      </div>
      <div class="contactme-item">
        <i class="fa-solid fa-envelope"></i>
        <div>
          <h3>Email Us</h3>
          <p>d12@gmail.com</p>

        </div>
      </div>
    </div>
    <div class="contact-form-container">
      <form class="contact-form" action="mailto:d12@gmail.com" method="post" enctype="text/plain">
        <div class="form-group">
          <input type="text" class="form-control" name="name" placeholder="Your Name" value="<?php echo $_SESSION['userName']; ?>" readonly>
          <input type="email" class="form-control" name="email" placeholder="Your Email" value="<?php echo $_SESSION['userEmail']; ?>" readonly>
        </div>
        <div class="form-group">
          <input type="text" class="form-control" name="subject" placeholder="Subject">
        </div>
        <div class="form-group">
          <textarea class="form-control" name="message" rows="5" placeholder="Message"></textarea>
        </div>
        <div class="form-group">
          <button type="submit" class="btn">Send Message</button>
        </div>
      </form>
    </div>


  </section>

  <footer>
    <?php include('./includes/footer.php'); ?>


    <?php

    ?>
  </footer>
</body>

</html>