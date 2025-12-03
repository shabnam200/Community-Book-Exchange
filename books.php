<?php
session_start();
require_once 'php/db_config.php';

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
  <title>Books | Community Book Exchange</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header>
    <nav class="navbar">
      <h1 class="logo">📚 Community Book Exchange</h1>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="books.php" class="active">Books</a></li>
        <li><a href="addbook.php">Add Book</a></li>
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

  <section class="books-section">
    <h2>Available Books</h2>
    <div class="books-grid">
      <?php
        
        $sql = "SELECT id, title, author, condition_status, cover_image, owner_id FROM books ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            $book_id = $row["id"];
            $book_title = htmlspecialchars($row["title"]);
            $book_author = htmlspecialchars($row["author"]);
            $book_condition = htmlspecialchars($row["condition_status"]);
            $image_path = htmlspecialchars($row["cover_image"]);
            $book_owner_id = $row["owner_id"];

            echo '<div class="book-card">';
            echo '<img src="' . $image_path . '" alt="' . $book_title . ' Cover">';
            echo '<h3>' . $book_title . '</h3>';
            echo '<p>by ' . $book_author . '</p>';
            echo '<p class="condition">Condition: ' . $book_condition . '</p>';

            if ($_SESSION["id"] == $book_owner_id) {
                echo '<button class="btn owner-btn" disabled>Your Book</button>';
            } else {
                echo '<form method="POST" action="php/request_exchange.php" style="display:inline;">';
                echo '<input type="hidden" name="book_id" value="' . $book_id . '">';
                echo '<button type="submit" class="btn exchange-btn">Request Exchange</button>';
                echo '</form>';
            }
            
            echo '</div>';
          }
        } else {
          echo "<p class='no-books-msg'>No books have been added yet! Be the first one.</p>";
        }
        $conn->close();
      ?>
    </div>
  </section>

  <footer>
    <p>© 2025 Community Book Exchange | Created by Shabnam</p>
  </footer>
  <script src="assets/js/scripts.js"></script>
</body>
</html>