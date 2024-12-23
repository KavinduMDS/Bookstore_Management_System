<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['user_role'])) {
    // Redirect to login page if not logged in
    header("Location: ../memberlogin.php");
    exit;
}

// Define profile page based on user role
$profilePage = "profile.php"; // Default for customer
if ($_SESSION['user_role'] === "admin") {
    $profilePage = "../profile_admin.php";
} elseif ($_SESSION['user_role'] === "order") {
    $profilePage = "../profile_order.php";
} elseif ($_SESSION['user_role'] === "inventory") {
    $profilePage = "../profile_inventory.php";
}

// Initialize variables
$eid = $firstname = $lastname = $address = $email = $password = $role = "";
$first_name_err = $last_name_err = $email_err = $password_err = $role_err =$adress_err= "";
$action = "insert"; // Default action for form
$search = ""; // For storing search keyword

// Check if a specific employee is being edited
if (isset($_GET['eid']) && $_GET['action'] == 'edit') {
    $eid = $_GET['eid'];
    $result = $conn->query("SELECT * FROM employee WHERE EID = $eid");
    $employee = $result->fetch_assoc();

    if ($employee) {
        // Populate variables with employee data for editing
        $firstname = $employee['firstname'];
        $lastname = $employee['lastname'];
        $address = $employee['address'];
        $email = $employee['email'];
        $password = $employee['password'];
        $role = $employee['role'];
        $action = "update"; // Set form action to update
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs with mysqli_real_escape_string
    $firstname = mysqli_real_escape_string($conn, trim($_POST['firstname']));
    $lastname = mysqli_real_escape_string($conn, trim($_POST['lastname']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    $role = mysqli_real_escape_string($conn, trim($_POST['role']));

    // Validate required fields
    if (empty($firstname)) {
        $first_name_err = "First name is required.";
    }
    if (empty($lastname)) {
        $last_name_err = "Last name is required.";
    }
    if (empty($email)) {
        $email_err = "Email is required.";
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address (e.g., johndoe@example.com).";
      }
      if (empty($email_err)) {
        $email = $conn->real_escape_string($email); // Sanitize input
        $sql_check_email = "SELECT * FROM employee WHERE email = '$email'";
        
        // Exclude current record if editing
        if (!empty($_POST['eid'])) {
            $eid = $conn->real_escape_string($_POST['eid']);
            $sql_check_email .= " AND EID != $eid";
        }

        $result = $conn->query($sql_check_email);
        if ($result->num_rows > 0) {
            $email_err = "This email is already registered.";
        }
    }
    if (empty($password)) {
        $password_err = "Password is required.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $password_err = "Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, and one number.";
    }
    if (empty($role)) {
        $role_err = "Role is required.";
    }
    if (empty($address)) {
        $adress_err = "Adress is required.";
    }

    // If no errors, proceed with database operations
    if (empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($password_err) && empty($role_err)) {
        if (isset($_POST['insert'])) {
            // Insert logic
            $sql = "INSERT INTO employee (firstname, lastname, address, email, password, role) 
                    VALUES ('$firstname', '$lastname', '$address', '$email', '$password', '$role')";
            $conn->query($sql);
            header("Location: index.php?action=insert_success");
            exit();
        } elseif (isset($_POST['update'])) {
            // Update logic
            $eid = mysqli_real_escape_string($conn, $_POST['eid']);
            $sql = "UPDATE employee 
                    SET firstname = '$firstname', lastname = '$lastname', address = '$address', 
                        email = '$email', password = '$password', role = '$role' 
                    WHERE EID = $eid";
            $conn->query($sql);
            header("Location: index.php?action=update_success");
            exit();
        } elseif (isset($_POST['delete'])) {
            // Delete logic
            $eid = mysqli_real_escape_string($conn, $_POST['eid']);
            $sql = "DELETE FROM employee WHERE EID = $eid";
            $conn->query($sql);
            header("Location: index.php?action=delete_success");
            exit();
        }
}
}

// Fetch employees for display if no search term
$employees = $conn->query("SELECT * FROM employee");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Configuration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Bookstore</a>
    <div class="navbar-nav ml-auto">
        <a class="nav-item nav-link" href="<?php echo $profilePage; ?>">Profile</a>
        <a class="nav-item nav-link" href="javascript:history.back()">Go Back</a>
    </div>
</nav>

<h2>Staff Configuration</h2>

<!-- Feedback messages -->
<?php if (isset($_GET['action'])): ?>
    <p style="color: green;">
        <?php if ($_GET['action'] == 'insert_success'): ?>
            Employee added successfully!
        <?php elseif ($_GET['action'] == 'update_success'): ?>
            Employee updated successfully!
        <?php elseif ($_GET['action'] == 'delete_success'): ?>
            Employee deleted successfully!
        <?php endif; ?>
    </p>
<?php endif; ?>

<!-- Form for adding/updating employees -->
<form method="POST" action="index.php">
    <input type="hidden" name="eid" value="<?php echo $eid; ?>">
    <div>
        <label>First Name:</label>
        <input type="text" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>">
        <span style="color: red;"><?php echo $first_name_err; ?></span>
    </div>
    <div>
        <label>Last Name:</label>
        <input type="text" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>">
        <span style="color: red;"><?php echo $last_name_err; ?></span>
    </div>
    <div>
        <label>Address:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>">
        <span style="color: red;"><?php echo $adress_err; ?></span>
    </div>
    <label>Email:</label>
        <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <span style="color: red;"><?php echo $email_err; ?></span>
</div>

    <div>
        <label>Password:</label>
        <input type="text" name="password" value="<?php echo htmlspecialchars($password); ?>">
        <span style="color: red;"><?php echo $password_err; ?></span>
    </div>
    <div>
        <label>Role:</label>
        <select name="role">
            <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
            <option value="order" <?php if ($role == 'order') echo 'selected'; ?>>Order</option>
            <option value="inventory" <?php if ($role == 'inventory') echo 'selected'; ?>>Inventory</option>
        </select>
        <span style="color: red;"><?php echo $role_err; ?></span>
    </div>
    <div>
        <?php if ($action == "insert"): ?>
            <button type="submit" name="insert">Add Employee</button>
        <?php else: ?>
            <button type="submit" name="update">Update Employee</button>
            <button type="submit" name="delete">Delete Employee</button>
        <?php endif; ?>
    </div>
</form>

<!-- Employee Table -->
<h3>Employee List</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Address</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>
    <?php while ($employee = $employees->fetch_assoc()): ?>
        <tr>
            <td><?php echo $employee['EID']; ?></td>
            <td><?php echo htmlspecialchars($employee['firstname']); ?></td>
            <td><?php echo htmlspecialchars($employee['lastname']); ?></td>
            <td><?php echo htmlspecialchars($employee['address']); ?></td>
            <td><?php echo htmlspecialchars($employee['email']); ?></td>
            <td><?php echo htmlspecialchars($employee['role']); ?></td>
            <td>
                <a href="index.php?eid=<?php echo $employee['EID']; ?>&action=edit">Edit</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
