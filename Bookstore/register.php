<?php
session_start(); // Start the session
include 'connect.php';

// Initialize error variables
$firstname_err = $lastname_err = $address_err = $contact_err = $email_err = $password_err = $confirm_password_err = $photo_err = '';
$registration_success = false;
$profileImagePath = 'uploads/default_profile.png'; // Default profile image

// Registration handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Get form data and trim extra spaces
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $photo = $_FILES['photo']['name'];

    // Validate fields
    if (empty($firstname)) {
        $firstname_err = "First name is required.";
    }
    if (empty($lastname)) {
        $lastname_err = "Last name is required.";
    }
    if (empty($address)) {
        $address_err = "Address is required.";
    }
    if (empty($contact)) {
        $contact_err = "Contact is required.";
    }
    if (empty($email)) {
        $email_err = "Email is required.";
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
      }
    if (empty($password)) {
        $password_err = "Password is required.";
    } elseif (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password)) {
        $password_err = "Password must be at least 8 characters long and contain at least one uppercase and one lowercase letter.";
    }
    if (empty($confirm_password)) {
        $confirm_password_err = "Confirm password is required.";
    } elseif ($password !== $confirm_password) {
        $confirm_password_err = "Passwords do not match.";
    }
    // Validate photo upload
    if (empty($photo)) {
        $photo_err = "Profile photo is required.";
    }

    // Check if email already exists in the database
    if (empty($email_err)) {
        $email = $conn->real_escape_string($email); // Sanitize the input to prevent SQL injection
        $sql_check_email = "SELECT * FROM customer WHERE email = '$email'"; // Directly inserting the email in the query string
        $result = $conn->query($sql_check_email);
        if ($result->num_rows > 0) {
            $email_err = "This email is already registered.";
        }
    }
    // If no errors, proceed with saving data
    if (empty($firstname_err) && empty($lastname_err) && empty($address_err) && empty($contact_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($photo_err)) {
        // Save uploaded photo and register user...
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($photo);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $sql = "INSERT INTO customer (firstname, lastname, address, contact, email, password, photo) 
                    VALUES ('$firstname', '$lastname', '$address', '$contact', '$email', '$password', '$target_file')";
            if ($conn->query($sql) === TRUE) {
                // Clear the form values after successful registration
                $firstname = $lastname = $address = $contact = $email = $password = $confirm_password = '';
                $photo = ''; // Reset the photo field
                $registration_success = true;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $photo_err = "Error uploading photo.";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore - Browse Books</title>
    <link rel="stylesheet" href="style/style.css"> <!-- Link to the CSS file for styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
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
                <li class="nav-item"><a class="nav-link" id="login_btn" href="login.php">LOGIN</a></li>
                <li class="nav-item"><a class="nav-link" id="register_btn" href="register.php">REGISTER</a></li>
            </ul>
            <?php endif; ?>
        </div>
    </nav>
</header>
<body>

<!-- Registration Form -->
<div class="container mt-5">
    <h2>Register</h2>

    <?php if ($registration_success): ?>
        <div class="alert alert-success">Registration successful!</div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="register" value="1">
    
    <div class="form-group">
        <label for="firstname">First Name:</label>
        <input type="text" name="firstname" id="firstname" class="form-control <?php echo !empty($firstname_err) ? 'is-invalid' : ''; ?>" value="<?php echo $firstname ?? ''; ?>">
        <?php if (!empty($firstname_err)) : ?>
            <span class="error"><?php echo $firstname_err; ?></span>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="lastname">Last Name:</label>
        <input type="text" name="lastname" id="lastname" class="form-control <?php echo !empty($lastname_err) ? 'is-invalid' : ''; ?>" value="<?php echo $lastname ?? ''; ?>">
        <?php if (!empty($lastname_err)) : ?>
            <span class="error"><?php echo $lastname_err; ?></span>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="address">Address:</label>
        <input type="text" name="address" id="address" class="form-control <?php echo !empty($address_err) ? 'is-invalid' : ''; ?>" value="<?php echo $address ?? ''; ?>">
        <?php if (!empty($address_err)) : ?>
            <span class="error"><?php echo $address_err; ?></span>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contact">Contact:</label>
        <input type="text" name="contact" id="contact" class="form-control <?php echo !empty($contact_err) ? 'is-invalid' : ''; ?>" value="<?php echo $contact ?? ''; ?>">
        <?php if (!empty($contact_err)) : ?>
            <span class="error"><?php echo $contact_err; ?></span>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="email">Email:</label>
        <input type="text" name="email" id="email" class="form-control <?php echo !empty($email_err) ? 'is-invalid' : ''; ?>" value="<?php echo $email ?? ''; ?>">
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

    <div class="form-group">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo !empty($confirm_password_err) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password ?? ''; ?>">
        <?php if (!empty($confirm_password_err)) : ?>
            <span class="error"><?php echo $confirm_password_err; ?></span>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="photo">Profile Photo:</label>
        <input type="file" name="photo" id="photo" class="form-control <?php echo !empty($photo_err) ? 'is-invalid' : ''; ?>">
        <?php if (!empty($photo_err)) : ?>
            <span class="error"><?php echo $photo_err; ?></span>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Register</button>
</form>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
