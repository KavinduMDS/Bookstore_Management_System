<?php
ob_start();
session_start();
include('../connect.php');

// Redirect if not logged in
if (!isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit;
}

// Profile page determination
$profilePage = "profile.php"; // Default for customer
if ($_SESSION['user_role'] === "admin") {
    $profilePage = "../profile_admin.php";
} elseif ($_SESSION['user_role'] === "order") {
    $profilePage = "../profile_order.php";
} elseif ($_SESSION['user_role'] === "inventory") {
    $profilePage = "../profile_inventory.php";
}

// Initialize variables
$name = $category = $author = $quantity = $price = $description = $image_path = $updateid = $category_id = "";
$name_err = $category_err = $author_err = $quantity_err = $price_err = $description_err = $category_id_err = $image_err = '';


$categories_sql = "SELECT * FROM category";
$categories_result = $conn->query($categories_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchBook = $conn->real_escape_string($_POST['searchBook']);
    $search_sql = "SELECT * FROM book WHERE name LIKE '%$searchBook%'";
    $search_result = $conn->query($search_sql);

    if ($search_result->num_rows > 0) {
        $book = $search_result->fetch_assoc();
        $name = $book['name'];
        $category_id = $book['category_id'];  // Store the category_id of the searched book
        $author = $book['author'];
        $quantity = $book['quantity'];
        $price = $book['price'];
        $description = $book['description'];
        $image_path = $book['image_path'];
        $updateid = $book['book_id'];
    } else {
        $name = $category_id = $author = $quantity = $price = $description = $image_path = $updateid = "";
        echo '<script>alert("No book found!");</script>';
    }

}

// Insert Book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $category_id = $conn->real_escape_string($_POST['category_id']);
    $author = $conn->real_escape_string($_POST['author']);
    $quantity = $conn->real_escape_string($_POST['quantity']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $image_path = '';
    
    // Validate input fields
    if (empty($name)) {
        $name_err = "Book name is required";
    }
    if (empty($category_id)) {
        $category_err = "Category is required";
    }
    if (empty($author)) {
        $author_err = "Author is required";
    }
    if (empty($quantity)) {
        $quantity_err = "Quantity is required";
    }
    if (empty($price)) {
        $price_err = "Price is required";
    }
    if (empty($description)) {
        $description_err = "Description is required";
    }
    if (empty($category_id)) {
        $category_id_err = "Category is required";
    }

    // Handle image upload
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_NO_FILE) {
        $image_err = "Image is required";  // Show error if no image is uploaded
    } elseif (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image_path']['tmp_name'];
        $imageName = $conn->real_escape_string($_FILES['image_path']['name']);
        $uploadFileDir = 'book_image/';
        $dest_path = $uploadFileDir . $imageName;
        if (move_uploaded_file($imageTmpPath, $dest_path)) {
            $image_path = $imageName;
        } else {
            $image_err = "Error uploading image file.";
        }
    }

    // Insert data if validation passes
    if (empty($name_err) && empty($category_err) && empty($author_err) && empty($quantity_err) && empty($price_err) && empty($description_err) && empty($category_id_err) && empty($image_err)) {
        $sql = "INSERT INTO book (name, category_id, author, quantity, price, description, image_path) 
                VALUES ('$name', '$category_id', '$author', '$quantity', '$price', '$description', '$image_path')";
        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("Book added successfully!");</script>';
            // Clear form after insertion
            $name = $category_id = $author = $quantity = $price = $description = $image_path = "";
        } else {
            echo '<script>alert("Error: ' . $conn->error . '");</script>';
        }
    }
}


    // Update Book
    if (isset($_POST['update'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $category_id = $conn->real_escape_string($_POST['category_id']);
        $author = $conn->real_escape_string($_POST['author']);
        $quantity = $conn->real_escape_string($_POST['quantity']);
        $price = $conn->real_escape_string($_POST['price']);
        $description = $conn->real_escape_string($_POST['description']);
        $updateid = $conn->real_escape_string($_POST['updateid']);
        
        // Validate input fields
        if (empty($name)) {
            $name_err = "Book name is required";
        }
        if (empty($author)) {
            $author_err = "Author is required";
        }
        if (empty($quantity)) {
            $quantity_err = "Quantity is required";
        }
        if (empty($price)) {
            $price_err = "Price is required";
        }
        if (empty($description)) {
            $description_err = "Description is required";
        }
        if (empty($category_id)) {
            $category_id_err = "Category is required";
        }

        $sql = "UPDATE book SET name='$name', category_id='$category_id', author='$author', quantity='$quantity', price='$price', description='$description' WHERE book_id='$updateid'";

        if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['image_path']['tmp_name'];
            $imageName = $conn->real_escape_string($_FILES['image_path']['name']);
            $uploadFileDir = 'book_image/';
            $dest_path = $uploadFileDir . $imageName;
            if (move_uploaded_file($imageTmpPath, $dest_path)) {
                $sql .= ", image_path='$imageName'";
            }
        }

        // Update data if validation passes
        if (empty($name_err) && empty($author_err) && empty($quantity_err) && empty($price_err) && empty($description_err) && empty($category_id_err)) {
            if ($conn->query($sql) === TRUE) {
                echo '<script>alert("Book updated successfully!");</script>';
                // Clear form after update
                $name = $category_id = $author = $quantity = $price = $description = $image_path = "";
            } else {
                echo '<script>alert("Error: ' . $conn->error . '");</script>';
            }
        }
    }

    // Delete Book
    if (isset($_POST['delete_book'])) {
        $book_id_to_delete = intval($_POST['delete_book_id']);
        $delete_sql = "DELETE FROM book WHERE book_id = $book_id_to_delete";
        if ($conn->query($delete_sql) === TRUE) {
            echo '<script>alert("Book deleted successfully!");</script>';
        } else {
            echo '<script>alert("Error: ' . $conn->error . '");</script>';
        }
    }

    $category_name_err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = $conn->real_escape_string($_POST['category_name']);

    // Validate input field
    if (empty($category_name)) {
        $category_name_err = "Category name is required.";
    } else {
        // Insert category into the database
        $sql = "INSERT INTO category (name) VALUES ('$category_name')";
        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("Category added successfully!");</script>';
            $category_name = ""; // Clear the form
        } else {
            echo '<script>alert("Error: ' . $conn->error . '");</script>';
        }
    }
}
// Fetch categories
$categories_sql = "SELECT * FROM category ORDER BY name ASC";
$categories_result = $conn->query($categories_sql);

// Fetch books for inventory with search filter
$search = isset($_POST['search_inventory']) ? $conn->real_escape_string($_POST['search_inventory']) : '';
$filter_sql = "
    SELECT b.book_id, b.name, c.name AS category_name, b.author, b.quantity, b.price, b.description 
    FROM book b
    LEFT JOIN category c ON b.category_id = c.categoryid
";
if (!empty($search)) {
    $filter_sql .= " WHERE 
        b.name LIKE '%$search%' OR 
        c.name LIKE '%$search%' OR 
        b.author LIKE '%$search%'
    ";
}
$filter_sql .= " ORDER BY b.name ASC";
$filter_result = $conn->query($filter_sql);

ob_end_flush();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bookstore</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
   
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Bookstore</a>
    <div class="navbar-nav ml-auto">
        <a class="nav-item nav-link" href="<?php echo $profilePage; ?>">Profile</a>
        <a class="nav-item nav-link" href="javascript:history.back()">Go Back</a>
    </div>
</nav>
<div class="container">
    <h2>Add a New Category</h2>
    <form method="POST" action="">
        <div class="form-group">
            <input 
                type="text" 
                name="category_name" 
                placeholder="Enter category name" 
                
                class="form-control"
            >
            <small class="text-danger"><?php echo $category_name_err; ?></small>
        </div>
        <br>
        <input type="submit" name="add_category" value="Add Category" class="btn btn-primary">
    </form>
</div>
    <!-- Search Form -->
<form method="POST" action="">
    <br>
    <div class="form-group">
        <input type="text" name="searchBook" placeholder="Enter book name">
        <input type="submit" name="search" value="Search">
    </div>
</form>

<div class="container">
    <h2>Manage Books</h2>
    <form method="POST" action="" enctype="multipart/form-data">
    <!-- Book Name -->
    <div class="form-group">
        <input type="text" name="name" placeholder="Enter book name" value="<?php echo $name; ?>">
        <small class="text-danger"><?php echo $name_err; ?></small>
    </div>
    <br>
    
    <!-- Category Dropdown -->
    <div class="form-group">
        <select name="category_id">
            <option value="">--Select a Category--</option>
            <?php while ($category = $categories_result->fetch_assoc()): ?>
                <option value="<?php echo $category['categoryid']; ?>" <?php if ($category['categoryid'] == $category_id) echo 'selected'; ?>>
                    <?php echo $category['name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <small class="text-danger"><?php echo $category_id_err; ?></small>
    </div>
    <br>

    <!-- Other Fields -->
    <div class="form-group">
        <input type="text" name="author" placeholder="Enter author name" value="<?php echo $author; ?>">
        <small class="text-danger"><?php echo $author_err; ?></small>
    </div>
    <br>
    <div class="form-group">
        <input type="number" name="quantity" placeholder="Enter quantity" value="<?php echo $quantity; ?>">
        <small class="text-danger"><?php echo $quantity_err; ?></small>
    </div>
    <br>
    <div class="form-group">
        <input type="number" name="price" placeholder="Enter price" value="<?php echo $price; ?>">
        <small class="text-danger"><?php echo $price_err; ?></small>
    </div>
    <br>
    <div class="form-group">
        <textarea name="description" placeholder="Enter description"><?php echo $description; ?></textarea>
        <small class="text-danger"><?php echo $description_err; ?></small>
    </div>
    <br>
    <div class="form-group">
        <input type="file" name="image_path" accept="image/*">
        <small class="text-danger"><?php echo $image_err; ?></small>
    </div>
    <br>
    <input type="submit" name="insert" value="Add Book">
    <input type="submit" name="update" value="Update Book">
    <input type="hidden" name="updateid" value="<?php echo $updateid; ?>">
</form>
</div>
<br><br>


<div class="container">
    <h2>Book Inventory</h2>

    <!-- Search Form -->
    <form method="POST" action="">
        <div class="form-group">
            <input 
                type="text" 
                name="search_inventory" 
                placeholder="Search books by name, category, or author" 
                class="form-control" 
                value="<?php echo isset($_POST['search_inventory']) ? $_POST['search_inventory'] : ''; ?>"
            >
        </div>
        <input type="submit" value="Search" class="btn btn-primary">
    </form>
    <br>
    

    <!-- Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Book ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Author</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Filter books based on search input
            $search = isset($_POST['search_inventory']) ? $conn->real_escape_string($_POST['search_inventory']) : '';

            $filter_sql = "
                SELECT b.book_id, b.name, c.name AS category_name, b.author, b.quantity, b.price, b.description 
                FROM book b
                LEFT JOIN category c ON b.category_id = c.categoryid
            ";

            if (!empty($search)) {
                $filter_sql .= " WHERE 
                    b.name LIKE '%$search%' OR 
                    c.name LIKE '%$search%' OR 
                    b.author LIKE '%$search%'
                ";
            }

            $filter_sql .= " ORDER BY b.name ASC";

            $filter_result = $conn->query($filter_sql);

            // Display filtered books
            if ($filter_result->num_rows > 0):
                while ($book = $filter_result->fetch_assoc()):
            ?>
                <tr>
                    <td><?php echo $book['book_id']; ?></td>
                    <td><?php echo $book['name']; ?></td>
                    <td><?php echo $book['category_name']; ?></td>
                    <td><?php echo $book['author']; ?></td>
                    <td><?php echo $book['quantity']; ?></td>
                    <td>Rs <?php echo $book['price']; ?></td>
                    <td><?php echo nl2br(htmlspecialchars($book['description'])); ?></td>
                    <td>
                        <!-- Delete Link -->
                        <form method="POST" action="">
                            <input type="hidden" name="delete_book_id" value="<?php echo $book['book_id']; ?>">
                            <input type="submit" name="delete_book" value="Delete" class="btn btn-danger btn-sm">
                        </form>
                    </td>
                </tr>
            <?php
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="8">No books found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

