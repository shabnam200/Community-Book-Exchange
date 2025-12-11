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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $book_id_to_delete = filter_var($_POST['delete_id'], FILTER_VALIDATE_INT);
    

    $sql_delete = "DELETE FROM books WHERE id = ? AND owner_id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("ii", $book_id_to_delete, $current_user_id);
        $stmt_delete->execute();
        $stmt_delete->close();
        header("location: mybooks.php?success=deleted");
        exit;
    }
}

include 'header.php'; 
?>

<section class="requests-section">
    <h2>My Shared Books Inventory</h2>
    <p style="text-align: center; margin-bottom: 20px;">Manage the books you have made available for exchange.</p>
    
    <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
        <p class="success-msg">Book successfully removed from your inventory.</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p class="error-msg">Error: <?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <div class="request-list-container">
        <?php
        $sql = "SELECT id, title, author, condition_status, is_available FROM books WHERE owner_id = ? ORDER BY created_at DESC";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $book_id = $row['id'];
                    $title = htmlspecialchars($row['title']);
                    $author = htmlspecialchars($row['author']);
                    $condition = htmlspecialchars($row['condition_status']);
                    $is_available = $row['is_available'];
                    
                    $available_text = $is_available ? 'Available' : 'Unavailable';
                    $available_class = $is_available ? 'status-available' : 'status-unavailable';

                    echo '<div class="request-card">';
                    echo '<div class="request-main-info">';
                    echo '<h4>' . $title . '</h4>';
                    echo '<p class="request-detail">by ' . $author . '</p>';
                    echo '<p class="request-detail">Condition: ' . $condition . '</p>';
                    echo '<p class="request-detail">Status: <span class="status-badge ' . $available_class . '">' . $available_text . '</span></p>';
                    echo '</div>'; 
                    
                    echo '<div class="request-actions">';
                    
                    // Edit Link
                    echo '<a href="edit_book.php?id=' . $book_id . '" class="btn accept-btn" style="background-color: #3a6ea5;">Edit</a>';

                    // Delete Form
                    echo '<form method="POST" action="mybooks.php" onsubmit="return confirm(\'Are you sure you want to completely remove this book entry? This action cannot be undone.\');">';
                    echo '<input type="hidden" name="delete_id" value="' . $book_id . '">';
                    echo '<button type="submit" class="btn reject-btn">Remove</button>';
                    echo '</form>';

                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p class='no-requests'>You haven't added any books to your inventory yet. <a href='addbook.php'>Add a book now</a>.</p>";
            }
            $stmt->close();
        }
        $conn->close();
        ?>
    </div>
</section>

<footer>
    <p>© 2025 Community Book Exchange | Created by Shabnam</p>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>