<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.html");
    exit;
}
$current_user_id = $_SESSION["id"];

require_once 'php/db_config.php'; 


$pending_count = 0;
$sql_count = "SELECT COUNT(*) AS pending_count FROM exchange_requests WHERE owner_user_id = ? AND status = 'pending'";

if ($stmt_count = $conn->prepare($sql_count)) {
    $stmt_count->bind_param("i", $current_user_id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $row_count = $result_count->fetch_assoc();
    $pending_count = $row_count['pending_count'];
    $stmt_count->close();
}

include 'header.php';
?>

<section class="form-container">
  <h2>Add a Book</h2>

  <?php if (isset($_GET['success'])): ?>
    <p class="success-msg">Book added successfully! <a href="mybooks.php">View your books</a>.</p>
  <?php elseif (isset($_GET['error'])): ?>
    <p class="error-msg">Error: <?php echo htmlspecialchars($_GET['error']); ?></p>
  <?php endif; ?>

  <form class="book-form" action="php/add_book.php" method="POST" enctype="multipart/form-data">
    <label>Book Title</label>
    <input type="text" name="title" placeholder="Enter book title" required>

    <label>Author</label>
    <input type="text" name="author" placeholder="Enter author name" required>

    <label>Genre</label>
   <input type="text" name="genre" placeholder="e.g. Mystery, Fiction" required>

    <label>Condition</label>
    <select name="condition" required>
      <option value="New">New</option>
      <option value="Like New">Like New</option>
      <option value="Good">Good</option>
      <option value="Used">Used</option>
      <option value="Poor">Poor</option>
    </select>

    <label>Description</label>
   <textarea name="description" placeholder="Edition, notes, special features..."></textarea>

    <label>Book Cover Image</label>
    <input type="file" name="cover_image" accept="image/*" required>

    <input type="hidden" name="owner_id" value="<?php echo $current_user_id; ?>">

    <button type="submit" class="btn submit-btn">List Book</button>
  </form>
</section>

<footer>
    <p>© 2025 Community Book Exchange | Created by Shabnam</p>
</footer>
</body>
</html>