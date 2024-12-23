<?php
session_start();
include 'connect.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT EID, role FROM employee WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password); // 'ss' specifies the variable types => 'string', 'string'
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Login successful
        $stmt->bind_result($EID, $role);
        $stmt->fetch();

        // Set session variables
        $_SESSION['user_id'] = $EID;
        $_SESSION['user_role'] = $role;

        // Redirect based on role
        switch ($role) {
            case 'admin':
                header("Location: profile_admin.php");
                break;
            case 'order':
                header("Location: profile_order.php");
                break;
            case 'inventory':
                header("Location: profile_inventory.php");
                break;
            default:
                header("Location: index.php"); // Fallback
        }
        exit();
    } else {
        // Login failed
        $error_message = "Invalid email or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="stylemember.css">
    <link rel="stylesheet" href="styles.css">
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
            
            <!-- Show login/register buttons if not logged in -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" id="member_btn" href="login.php">LOGIN</a></li>
                
                <li class="nav-item"><a class="nav-link" id="register_btn" href="register.php">REGISTER</a></li>
            </ul>
            
        </div>
    </nav>
</header>
    <div class="login-container">
        <h5 class="text-center">Login</h5>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <form action="" method="post">
            <input type="hidden" name="login" value="1">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>
</body>
</html>
