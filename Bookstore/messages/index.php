<?php
session_start();
include '../connect.php';


// Define profile page based on user role
$profilePage = "../profile.php"; // Default for customer


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inquiry and Feedback</title>
  <style>
     
    body {
      font-family: sans-serif;
      background-color: #f9f9f9;
    }
    .container {
      width: 500px;
      margin: 50px auto;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background-color: #fff;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #200393;
    }
    label {
      display: block;
      margin-bottom: 5px;
      color: #333;
    }
    input[type="text"],
    textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 3px;
      box-sizing: border-box;
    }
    textarea {
      height: 100px;
    }
    button {
      background-color: #200393;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
    }
    button:hover {
      background-color: #200380;
    }
  </style>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Bookstore</a>
        <div class="navbar-nav ml-auto">
            <a class="nav-item nav-link" href="<?php echo $profilePage; ?>">Profile</a>
            <a class="nav-item nav-link" href="javascript:history.back()">Go Back</a>
        </div>
    </nav>
  <div class="container">
    <h2>Inquiry and Feedback</h2>
    <form method="post" action="submit_feedback.php">
      <input type="text" id="subject" name="subject" placeholder="Subject" required>
      <textarea id="message" name="message" placeholder="Message" required></textarea>
      <button type="submit">Submit</button>
    </form>
  </div>
</body>
</html>
