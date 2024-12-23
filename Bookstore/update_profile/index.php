<?php
include '../connect.php';
$profilePage = "../profile.php";

session_start();
$userId = $_SESSION['user_id']; // Adjust as needed for user session management

// Fetch customer data
$sql = "SELECT * FROM customer WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Initialize variables for error messages
$firstname_err = $lastname_err = $address_err = $contact_err = $email_err = $password_err = $photo_err = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and fetch POST data
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // File upload handling
    $photo = $user['photo']; // Default to current photo
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $fileName = basename($_FILES['profile_picture']['name']);
        $targetFilePath = $uploadDir . $fileName;
        $relativePath = 'uploads/' . $fileName; // Save relative path to database

        // Validate file type
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
                $photo = $relativePath; // Update photo variable to relative path
            } else {
                $photo_err = "Failed to upload the profile picture. Please try again.";
            }
        } else {
            $photo_err = "Invalid file type. Please upload an image file (JPG, JPEG, PNG, GIF).";
        }
    }

    // Validation
    $isValid = true;

    if (empty($firstname)) {
        $firstname_err = "First name is required.";
        $isValid = false;
    }
    if (empty($lastname)) {
        $lastname_err = "Last name is required.";
        $isValid = false;
    }
    if (empty($address)) {
        $address_err = "Address is required.";
        $isValid = false;
    }
    if (empty($contact) || !preg_match('/^\d{10}$/', $contact)) {
        $contact_err = "Contact number must be a valid 10-digit number.";
        $isValid = false;
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
        $isValid = false;
    }
    if (!empty($password) && strlen($password) < 8) {
        $password_err = "Password must be at least 8 characters long.";
        $isValid = false;
    }

    // If validation passes, proceed with updating
    if ($isValid) {
        // Keep the existing password if the new password is not provided
        if (empty($password)) {
            $password = $user['password'];
        }

        // Update the customer data
        $updateSql = "UPDATE customer SET firstname=?, lastname=?, address=?, contact=?, email=?, password=?, photo=? WHERE id=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssssssi", $firstname, $lastname, $address, $contact, $email, $password, $photo, $userId);
        $stmt->execute();

        $successMessage = "Your information has been updated successfully!";
        // Refresh the user data
        $sql = "SELECT * FROM customer WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Edit Profile</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Bookstore</a>
    <div class="navbar-nav ml-auto">
        <a class="nav-item nav-link" href="<?php echo $profilePage; ?>">Profile</a>
    </div>
</nav>

<div class="form-container">
    <h2>Edit Your Profile</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label>First Name:</label>
        <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>">
        <span style="color: red;"><?php echo $firstname_err; ?></span>

        <label>Last Name:</label>
        <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>">
        <span style="color: red;"><?php echo $lastname_err; ?></span>

        <label>Address:</label>
        <textarea name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
        <span style="color: red;"><?php echo $address_err; ?></span>

        <label>Contact:</label>
        <input type="text" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>">
        <span style="color: red;"><?php echo $contact_err; ?></span>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
        <span style="color: red;"><?php echo $email_err; ?></span>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter new password (optional)">
        <span style="color: red;"><?php echo $password_err; ?></span>

        <label>Profile Picture:</label>
        <input type="file" name="profile_picture" accept="image/*">
        <span style="color: red;"><?php echo $photo_err; ?></span>

        <button type="submit">Update Information</button>
    </form>
    <?php if ($successMessage): ?>
        <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>
</div>
</body>
</html>
