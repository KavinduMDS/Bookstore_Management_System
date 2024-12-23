<?php
session_start(); // Start the session
include 'connect.php';

// Initialize variables
$registration_success = false;
$login_success = false;
$profileImagePath = 'uploads/default_profile.png'; // Default profile image

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the profile image path from the database
    $query = "SELECT photo FROM customer WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($profile_image);
    if ($stmt->fetch() && !empty($profile_image)) {
        $profileImagePath = $profile_image; // Use the user's profile image if available
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header with Navigation Bar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                <li class="nav-item"><a class="nav-link" href="search.php">SEARCH</a></li>
                <li class="nav-item"><a class="nav-link" href="#newArrival">NEW ARRIVAL</a></li>
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




<!-- JavaScript to handle modal toggle and profile dropdown -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById("profileImg")?.addEventListener("click", function() {
    var dropdown = document.getElementById("profileDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
});

// Close the dropdown if clicking outside
document.addEventListener("click", function(event) {
    var profileImg = document.getElementById("profileImg");
    var dropdown = document.getElementById("profileDropdown");
    if (dropdown && dropdown.style.display === "block" && event.target !== profileImg) {
        dropdown.style.display = "none";
    }
});
    // Show login modal
    document.getElementById('login_btn').addEventListener('click', function() {
        $('#loginModal').modal('show');
    });

    // Show register modal
    document.getElementById('register_btn').addEventListener('click', function() {
        $('#registerModal').modal('show');
    });

    // Profile dropdown toggle
    document.getElementById("profileImg")?.addEventListener("click", function() {
        var dropdown = document.getElementById("profileDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    });
</script>

<div class="content">
        <div class="left-content">
            <h2>FOR ALL YOUR</h2>
            <h2>READING NEEDS</h2>
            <p>Welcome to our online bookstore,<br>
            where every book lover's need.<br>
            Explore<br>
            books for various genres</p>
            <a href="book_page/index.php" class="buy-now-link">
            <button class="buy-now-btn">BUY NOW</button>
        </a> 
        </div>
        <div class="right-content slideshow-container">
        <img src="image1.jpg" alt="Image 1" class="slideshow-image">
        <img src="image2.jpg" alt="Image 2" class="slideshow-image">
        <img src="image3.jpg" alt="Image 3" class="slideshow-image">
</div>
    </div>
    <div id ="newArrival">
    <h1>New Arrivals</h1>
    </div>
    
    <?php
// Assuming you have a database connection already established
$query = "SELECT book_id, name, image_path FROM book ORDER BY book_id DESC LIMIT 6";
$result = mysqli_query($conn, $query);

// Fetch and display the latest 6 books
echo "<div class='row'>"; // Start the row to hold the columns
while ($row = mysqli_fetch_assoc($result)) {
    $bookid = $row['book_id'];
    $name = $row['name'];
    $image = $row['image_path'];
    echo "
    <div class='col-md-4 mb-4'>  <!-- Each book in a 4-column grid, 3 items per row -->
        <div class='card'>
            <a href='page{$bookid}.html'>
                <img src='book/book_image/{$image}' class='card-img-top' alt='{$name}'>
            </a>
            <div class='card-body'>
                <h5 class='card-title'>{$name}</h5>
            </div>
        </div>
    </div>"; // End of col-md-4
}
echo "</div>"
?>
    <footer>
        <p>&copy; 2024 Book Wave. All rights reserved.</p>
        <div class="footer-links">
            <a href="privacy.php">Privacy Policy</a>
            <a href="terms.php">Terms of Service</a>
            <a href="contact.php">Contact Us</a>
        </div>
    </footer>
    <script>
    const images = document.querySelectorAll('.slideshow-image');
    let currentIndex = 0;

    function rotateImages() {
        // Calculate new positions based on currentIndex
        images.forEach((img, i) => {
            const position = (i - currentIndex + images.length) % images.length;
            img.style.transform = `translateX(${position * 100}%)`;
        });

        // Update the current index for the next rotation
        currentIndex = (currentIndex + 1) % images.length;
    }

    // Rotate every 3 seconds
    setInterval(rotateImages, 3000);
</script>

</body>
</html>

