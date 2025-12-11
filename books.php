<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'php/db_config.php';

$pending_count = 0;
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $current_user_id = $_SESSION["id"];

    $sql_count = "SELECT COUNT(*) AS pending_count FROM exchange_requests WHERE owner_user_id = ? AND status = 'pending'";

    if ($stmt_count = $conn->prepare($sql_count)) {
        $stmt_count->bind_param("i", $current_user_id);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $row_count = $result_count->fetch_assoc();
        $pending_count = $row_count['pending_count'];
        $stmt_count->close();
    }
}

include 'header.php';

$search_term = '';
$search_condition = '';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $search_condition = " AND (title LIKE ? OR author LIKE ?) ";
    $param_value = '%' . $search_term . '%';
}

?>

  <section class="books-section">
    <h2>Available Books</h2>
    
    <div class="search-container">
        <form method="GET" action="books.php" class="book-search-form">
            <input type="text" name="search" placeholder="Search by book title or author..." value="<?php echo htmlspecialchars($search_term); ?>">
            <button type="submit" class="btn search-btn">Search</button>
            <?php if (!empty($search_term)): ?>
                <a href="books.php" class="btn reset-btn">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="books-grid">
      <?php
        
        $sql = "SELECT 
                    title, 
                    author, 
                    MIN(cover_image) AS cover_image, 
                    SUM(is_available) AS total_available_copies,
                    GROUP_CONCAT(DISTINCT condition_status) AS all_conditions 
                FROM books 
                WHERE is_available > 0 
                {$search_condition} 
                GROUP BY title, author
                ORDER BY title ASC";

        if (!empty($search_condition)) {
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ss", $param_value, $param_value);
                $stmt->execute();
                $result = $stmt->get_result();
            }
        } else {
            $result = $conn->query($sql);
        }

        if (isset($result) && $result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            $book_title = htmlspecialchars($row["title"]);
            $book_author = htmlspecialchars($row["author"]);
            $available_copies = $row["total_available_copies"];
            $image_path = htmlspecialchars($row["cover_image"]);
            $all_conditions = htmlspecialchars($row["all_conditions"]);

            echo '<div class="book-card">';
            echo '<img src="' . $image_path . '" alt="' . $book_title . ' Cover">';
            echo '<h3>' . $book_title . '</h3>';
            echo '<p>by ' . $book_author . '</p>';
            echo '<p class="condition">Conditions available: ' . $all_conditions . '</p>';
            echo '<p class="copies-count">Available Copies: <strong>' . $available_copies . '</strong></p>';

            echo '<a href="book_detail.php?title=' . urlencode($book_title) . '&author=' . urlencode($book_author) . '" class="btn exchange-btn">View Copies (' . $available_copies . ')</a>';
            
            echo '</div>';
          }
        } else {
          echo "<p class='no-books-msg'>No books found matching your criteria. Try a different search term.</p>";
        }
        
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
      ?>
    </div>
  </section>

  <footer>
    <p>© 2025 Community Book Exchange | Created by Shabnam</p>
  </footer>
</body>
</html>