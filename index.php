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
?>

<div class="content">
<section class="hero">
<h2>Share Stories, Swap Books, Connect People</h2>
<p>Join your community of book lovers — give a book, take a book, and make new friends along the way!</p>
<div class="hero-actions">
    <a href="books.php" class="btn primary-btn">Browse Books</a>
    <?php
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        echo '<a href="addbook.php" class="btn secondary-btn">Add Your Book</a>';
    } else {
        echo '<a href="login.html" class="btn secondary-btn">Get Started</a>';
    }
    ?>
</div>
</section>

<section class="featured-books">
    <h3>Recently Added</h3>
    <div class="books-grid">
        <?php
        $sql = "SELECT title, author, condition_status, cover_image FROM books WHERE is_available = 1 ORDER BY created_at DESC LIMIT 3";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $book_title = htmlspecialchars($row["title"]);
                $book_author = htmlspecialchars($row["author"]);
                $book_condition = htmlspecialchars($row["condition_status"]);
                $image_path = htmlspecialchars($row["cover_image"]);

                echo '<div class="book-card">';
                echo '<img src="' . $image_path . '" alt="' . $book_title . ' Cover">';
                echo '<h3>' . $book_title . '</h3>';
                echo '<p>by ' . $book_author . '</p>';
                echo '<p class="condition">Condition: ' . $book_condition . '</p>';
                echo '<a href="books.php" class="btn exchange-btn">View Details</a>';
                echo '</div>';
            }
        } else {
            echo "<p>No books have been added yet! Be the first one.</p>";
        }
        $conn->close();
        ?>
    </div>
</section>

</div>

<footer>
    <p>© 2025 Community Book Exchange | Created by Shabnam</p>
</footer>
</body>
</html>