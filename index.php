<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition System | Login & Register</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <div class="login-body">
    <div class="container">
        <h2>Nutrition Recommender System</h2>

        <!-- Display Session Message -->
        <?php
        session_start();
        if (isset($_SESSION['message'])) {
            echo "<div class='alert'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']); // Clear message after displaying
        }
        ?>

        <!-- Registration Form -->
        <div id="register-form" style="display: none;">
            <h3>Register</h3>
            <form action="./pages/auth.php" method="POST">
                <input type="text" name="username" placeholder="Enter Username" required>
                <input type="email" name="email" placeholder="Enter Email" required>
                <input type="password" name="password" placeholder="Enter Password" required>
                <button type="submit" name="register">Register</button>
            </form>
            <p>Already have an account? <a href="#" onclick="showLogin()">Login</a></p>
        </div>

        <!-- Login Form -->
        <div id="login-form">
            <h3>Login</h3>
            <form action="./pages/auth.php" method="POST">
                <input type="text" name="identifier" placeholder="Enter Email or Username" required>
                <input type="password" name="password" placeholder="Enter Password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <p><a href="#">Forgot Password?</a></p>
            <p>Don't have an account? <a href="#" onclick="showRegister()">Register</a></p>
        </div>
    </div>
    <h2>Vicky</h2>
    </div>

    <script src="./assets/js/script.js"></script>
</body>
</html>
