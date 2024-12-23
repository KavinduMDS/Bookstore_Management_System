<?php
session_start(); // Start the session
include 'connect.php';

// Initialize error variables
$email_err = $password_err = '';
$login_success = false;

// Login handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate fields
    if (empty($email)) {
        $email_err = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $password_err = "Password is required.";
    }

    // If no errors, proceed with checking login credentials
    if (empty($email_err) && empty($password_err)) {
        // Check if the user exists
        $sql_check_user = "SELECT * FROM customer WHERE email = ?";
        $stmt = $conn->prepare($sql_check_user);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify the password
            if ($password == $user['password']) { // Make sure to hash the password in production
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
                $login_success = true;
                // Redirect to the homepage
                header("Location: index.php");
                exit();
            } else {
                $password_err = "Incorrect password.";
            }
        } else {
            $email_err = "No account found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .error {
            color: red;
            font-size: 0.9em;
        }

        .is-invalid {
            border-color: red;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#"><h1>Book Wave</h1></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">HOME</a></li>
                <li class="nav-item"><a class="nav-link" href="book_page/index.php">BOOKS</a></li>
               
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Display profile picture if logged in -->
            <ul class="navbar-nav ml-auto" id="profile">
                <li class="nav-item dropdown">
                    <img src="<?php echo $profileImagePath; ?>" alt="Profile" id="profileImg" class="rounded-circle" style="width: 40px; height: 40px; cursor: pointer;">
                    <div class="dropdown-content" id="profileDropdown" style="display: none;">
                        <a href="profile.php">View Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
            <?php else: ?>
            <!-- Show login/register buttons if not logged in -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" id="member_btn" href="memberlogin.php">MEMBER LOGIN</a></li>
                
                <li class="nav-item"><a class="nav-link" id="register_btn" href="register.php">REGISTER</a></li>
            </ul>
            <?php endif; ?>
        </div>
    </nav>
</header>

<!-- Login Form -->
<div class="container mt-5">
    <h2>Login</h2>

    

    <form action="" method="post">
    <input type="hidden" name="login" value="1">
    
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" class="form-control <?php echo !empty($email_err) ? 'is-invalid' : ''; ?>" value="<?php echo $email ?? ''; ?>">
        <?php if (!empty($email_err)) : ?>
            <span class="error"><?php echo $email_err; ?></span>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" class="form-control <?php echo !empty($password_err) ? 'is-invalid' : ''; ?>" value="<?php echo $password ?? ''; ?>">
        <?php if (!empty($password_err)) : ?>
            <span class="error"><?php echo $password_err; ?></span>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <div class="mt-3">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
