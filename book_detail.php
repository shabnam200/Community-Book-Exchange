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

if (!isset($_GET['title']) || !isset($_GET['author'])) {
    header("location: books.php");
    exit;
}

$title = urldecode($_GET['title']);
$author = urldecode($_GET['author']);
?>

<section class="requests-section book-detail-section">
    <h2>Available Copies of: "<?php echo htmlspecialchars($title); ?>"</h2>
    <h3>by <?php echo htmlspecialchars($author); ?></h3>

    <div class="copy-list-container">
        <?php
        $sql = "SELECT b.id, b.condition_status, u.fullname, u.id AS owner_id 
                FROM books b
                JOIN users u ON b.owner_id = u.id
                WHERE b.title = ? AND b.author = ? AND b.is_available > 0";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $title, $author);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $copy_id = $row['id'];
                    $condition = htmlspecialchars($row['condition_status']);
                    $owner_name = htmlspecialchars($row['fullname']);
                    $owner_id = $row['owner_id'];
                    
                    echo '<div class="request-card copy-card">';
                    echo '<div class="request-main-info">';
                    echo '<h4>Condition: ' . $condition . '</h4>';
                    echo '<p class="request-detail">Shared by: <strong>' . $owner_name . '</strong></p>';
                    echo '</div>'; 
                    echo '<div class="request-actions">';

                    if (isset($_SESSION["id"]) && $_SESSION["id"] == $owner_id) {
                        echo '<button class="btn owner-btn" disabled>Your Copy</button>';
                    } elseif (isset($_SESSION["id"])) {
                        echo '<form method="POST" action="php/request_exchange.php" style="display:inline;">';
                        echo '<input type="hidden" name="book_id" value="' . $copy_id . '">';
                        echo '<button type="submit" class="btn accept-btn">Request This Copy</button>';
                        echo '</form>';
                    } else {
                        echo '<a href="login.html" class="btn accept-btn">Login to Request</a>';
                    }
                    
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p class='no-requests'>No copies are currently available.</p>";
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
</body>
</html>