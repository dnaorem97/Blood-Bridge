<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="icon" href="./favicon/bloodtype_30dp_8B1A10_FILL0_wght400_GRAD0_opsz24.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/signup.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.css">
</head>

<body>
    <!-- <h2>Signup</h2> -->
   <section>
        <!-- <div class="image_container">
            Optimized: Ensure the image is included or remove the comment if unused
            <img src="./images/171.jpg" alt="">
        </div> -->
        <div class="form_container">
            <form action="signup.php" method="POST">
                <fieldset>
                    <legend>SignUp Now</legend>

                    <label for="name">Name:</label>
                    <input type="text" name="name" placeholder="Enter your name" required>

                    <label for="email">Email:</label>
                    <input type="email" name="email" placeholder="Enter your email" required><br>
                    <label for="UserGender">Select Your Gender:</label><br>
                        <input type="radio" id="male" name="user_gender" value="male" required>
                        <label for="male">Male</label>
                        <input type="radio" id="female" name="user_gender" value="female">
                        <label for="female">Female</label>
                        <input type="radio" id="other" name="user_gender" value="other">
                        <label for="other">Others</label><br><br>

                    <label for="password">Password:</label>
                    <input type="password" name="password" placeholder="Enter your password" required>

                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" placeholder="Confirm your password" required>

                    <div class="user_role">
                        <label for="role" id="role_label">Signup as:</label>
                        <select name="role" required>
                            <option value="donor" selected>User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <p>Account already exist? <a href="./login.php">LOG IN</a></p>
                    <button type="submit" name="signup">Sign Up</button>

                    <div class="social-icons">
                        <a href="#" class="fab fa-facebook"></a>
                        <a href="#" class="fab fa-twitter"></a>
                        <a href="#" class="fab fa-instagram"></a>
                    </div>
                </fieldset>
            </form>
        </div>
    </section>
    <?php
    // Database connection
    include('./db_connection/db.php');

    if (isset($_POST['signup'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $gender = $_POST['user_gender'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role = $_POST['role'];

        if ($password !== $confirm_password) {
            echo "Passwords do not match!";
            exit;
        }
        $check_user_query = "SELECT * FROM  users WHERE email = '$email'";
        $check_user_result = $conn->query($check_user_query);
        if($check_user_result->num_rows>0){
            echo "<script>alert('Email already exists')</script>";
        }else{
            $hashed_password = password_hash($password,PASSWORD_DEFAULT);
            $user_insert_query = $conn->prepare("INSERT INTO users(name, email,gender, password, role) VALUES(?,?,?,?,?)");
            $user_insert_query->bind_param("sssss", $name, $email,$gender, $hashed_password,$role);

            if($user_insert_query->execute()){
                echo "<script>alert('Signup successful! You can now log in.')</script>";
            // Redirect to login page after successful signup
            header('Location: login.php');
        } else {
            echo "Error: " . mysqli_error($conn);
        }
            }
        }


    //     // Password validation
       

    //     // Hash the password for security
    //     $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //     // Insert user data into the database
    //     $myquery = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";
    //     if ($conn->query($myquery) === TRUE) {
    //         echo "Signup successful! You can now log in.";
    //         // Redirect to login page after successful signup
    //         header('Location: login.php');
    //     } else {
    //         echo "Error: " . mysqli_error($conn);
    //     }
    // }
    ?>

</body>

</html>