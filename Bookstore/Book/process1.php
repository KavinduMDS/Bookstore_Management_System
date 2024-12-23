<?php

ob_start();
include('../connect.php');


// Initialize variables for form fields

$name = $category = $author = $quantity = $price = $description = $updateid =$category_id= "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Category
    if (isset($_POST['add_category'])) {
        $category_name = $conn->real_escape_string($_POST['category_name']);
        $insert_category_sql = "INSERT INTO category (name) VALUES ('$category_name')";
        if ($conn->query($insert_category_sql) === TRUE) {
            header("Location: index.php?action=add_category_success");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Handle Insert (Add Book)
if (isset($_POST['insert'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image_path = '';

    // Handle image upload
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image_path']['tmp_name'];
        $imageName = mysqli_real_escape_string($conn, $_FILES['image_path']['name']);
        $uploadFileDir = 'book_image/'; // Ensure this directory exists and is writable
        $dest_path = $uploadFileDir . $imageName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($imageTmpPath, $dest_path)) {
            $image_path = $imageName; // Save only the filename for database
        } else {
            echo "Error uploading file.";
        }
    }

    // Insert query
    $sql = "INSERT INTO book (name, category_id, author, quantity, price, description, image_path) 
            VALUES ('$name', '$category_id', '$author', '$quantity', '$price', '$description', '$image_path')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?action=insert");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

}

// Handle Search
if (isset($_POST['search'])) {
    $searchTerm = $_POST['searchBook'];

    // Search query with JOIN to include category name
    $sql = "
        SELECT b.*, c.name AS category_name 
        FROM book b 
        LEFT JOIN category c ON b.category_id = c.categoryid 
        WHERE b.name LIKE '%$searchTerm%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $category = $row['category_name']; // Get category name instead of ID
        $author = $row['author'];
        $quantity = $row['quantity'];
        $price = $row['price'];
        $description = $row['description'];
        $image_path = $row['image_path'];
        $updateid = $row['book_id']; // Use book_id here
        header("Location: index.php?search=yes&name=$name&category=$category&author=$author&quantity=$quantity&price=$price&description=$description&image_path=$image_path&id=$updateid");
        exit;
    } else {
        header("Location: index.php?search=no");
        exit;
    }
}

// Handle Update
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category_id']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $updateid = mysqli_real_escape_string($conn, $_POST['updateid']);

    if (!empty($updateid)) {
        // Prepare the update SQL
        $sql = "UPDATE book SET name='$name', category_id='$category', author='$author', quantity='$quantity', price='$price', description='$description' WHERE book_id='$updateid'";

        // Handle image upload (if a new image is provided)
        if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['image_path']['tmp_name'];
            $imageName = mysqli_real_escape_string($conn, $_FILES['image_path']['name']);
            $uploadFileDir = 'book_image/';
            $dest_path = $uploadFileDir . $imageName;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($imageTmpPath, $dest_path)) {
                $sql .= ", image_path='$imageName'"; // Update image path in SQL
            } else {
                echo "Error uploading file.";
            }
        }

        if ($conn->query($sql) === TRUE) {
            header("Location: index.php?update=yes");
            exit;
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "No ID specified for update.";
    }
}

// Handle Delete


if (isset($_POST['delete_book'])) {
    // Sanitize the book ID to prevent SQL injection
    $book_id_to_delete = intval($_POST['delete_book_id']);
    
    // Delete query
    $delete_sql = "DELETE FROM book WHERE book_id = $book_id_to_delete";

    if ($conn->query($delete_sql) === TRUE) {
        // Redirect to the main page (for example, 'index.php') after successful deletion
        header("Location: index.php?delete=success");
        exit;
    } else {
        // Error handling if the query fails
        echo '<script>alert("Error deleting book: ' . $conn->error . '");</script>';
    }
}

$conn->close();
ob_end_flush();
?>
