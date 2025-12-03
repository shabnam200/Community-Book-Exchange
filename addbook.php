<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  
    header("location: login.html");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add a Book | Community Book Exchange</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

<header>
  <nav class="navbar">
    <h1 class="logo">📚 Community Book Exchange</h1>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="books.php">Books</a></li>
      <li><a href="addbook.php" class="active">Add Book</a></li>
      <li><a href="requests.php">Requests</a></li>
      <li><a href="about.php">About</a></li>
      <li>
    <?php
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            echo '<a href="php/logout.php">Logout ('.$_SESSION["fullname"].')</a>';
        } else {
            echo '<a href="login.html">Login</a>';
        }
    ?>
</li>
    </ul>
  </nav>
</header>

<section class="form-container">
  <h2>Add a Book</h2>

  <form class="book-form" action="php/add_book.php" method="POST" enctype="multipart/form-data">
    <label>Book Title</label>
    <input type="text" name="title" placeholder="Enter book title" required>

    <label>Author</label>
    <input type="text" name="author" placeholder="Enter author name" required>

    <label>Genre</label>
   <input type="text" name="genre" placeholder="e.g. Mystery, Fiction" required>

    <label>Condition</label>
    <select name="condition" required>
      <option>New</option>
      <option>Good</option>
      <option>Used</option>
    </select>

    <label>Description</label>
   <textarea name="description" placeholder="Edition, notes, special features..."></textarea>

    <label>Book Cover Image</label>
   <input type="file" name="cover_image">

    <button type="submit" class="btn submit-btn">Submit Book</button>
  </form>
</section>

<footer>
  <p>© 2025 Community Book Exchange | Created by Shabnam</p>
</footer>
<script src="assets/js/scripts.js?v=2"></script>
</body>
</html>
