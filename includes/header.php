<ul>
    <li>
        <a href="home.php" class="<?php echo ($current_page === 'home.php') ? 'active' : ''; ?>">
            <span id="home_icon"><i class="fa-solid fa-house"></i></span>Home
        </a>
    </li>
    <li>
        <a href="blood_stock.php" class="<?php echo ($current_page === 'blood_stock.php') ? 'active' : ''; ?>">Blood Stock</a>
    </li>
    <li>
        <a href="./request.php" class="<?php echo($current_page==='request.php')? 'active': '' ?>">Request</a>
    </li>
    <li>
        <a href="donor_registration.php" class="<?php echo ($current_page === 'donor_registration.php') ? 'active' : ''; ?>">Bookings</a>
    </li>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'donor'): ?>
        <li id="donorActions">
            <a href="donor_dashboard.php" class="<?php echo ($current_page === 'donor_dashboard.php') ? 'active' : ''; ?>">User Dashboard</a>
        </li>
    <?php endif; ?>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li id="adminActions">
            <a href="testAdmin.php" class="<?php echo ($current_page === 'testAdmin.php') ? 'active' : ''; ?>">Admin Actions</a>
        </li>
    <?php endif; ?>

    <li>
        <span id="uname_icon">
            <i class="fa-solid fa-user"></i>
        </span>
        <?php
        if (isset($_SESSION['userName'])) {
            echo " Welcome, " . $_SESSION['userName'];
        }
        ?>
    </li>
    <li>
        <a href="logout.php"><span id="log_out"><i class="fa-solid fa-sign-out"></i></span></a>
    </li>
</ul>
