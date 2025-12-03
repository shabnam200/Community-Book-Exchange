<?php
session_start();
require_once 'php/db_config.php';

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.html");
    exit;
}
$current_user_id = $_SESSION["id"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Requests | Community Book Exchange</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header>
    <nav class="navbar">
      <h1 class="logo">📚 Community Book Exchange</h1>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="books.php">Books</a></li>
        <li><a href="addbook.php">Add Book</a></li>
        <li><a href="requests.php" class="active">Requests</a></li>
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

  <section class="requests-section">
    <h2>My Exchange Requests</h2>

   <div class="content">  <section class="requests-section">
        <h3>Requests Received for My Books</h3>
        <?php
        $sql_received = "SELECT r.request_id, b.title AS book_title, u.fullname AS requester_name, r.status, r.requested_at
                         FROM exchange_requests r
                         JOIN books b ON r.requested_book_id = b.id
                         JOIN users u ON r.requester_user_id = u.id
                         WHERE r.owner_user_id = ? AND r.status = 'pending'
                         ORDER BY r.requested_at DESC";

        if ($stmt_received = $conn->prepare($sql_received)) {
            $stmt_received->bind_param("i", $current_user_id);
            $stmt_received->execute();
            $result_received = $stmt_received->get_result();

            if ($result_received->num_rows > 0) {
                while ($row = $result_received->fetch_assoc()) {
                    $request_id = $row['request_id'];
                    $book_title = htmlspecialchars($row['book_title']);
                    $requester_name = htmlspecialchars($row['requester_name']);
                    $requested_at = date('M j, Y', strtotime($row['requested_at']));
                    
                    echo '<div class="request-card received">';
                    echo '<h4>Request for: ' . $book_title . '</h4>';
                    echo '<p>From: <strong>' . $requester_name . '</strong></p>';
                    echo '<p>Requested On: ' . $requested_at . '</p>';
                    echo '<div class="request-actions">';
                    
                    // Accept Form
                    echo '<form method="POST" action="php/handle_request.php" style="display:inline;">';
                    echo '<input type="hidden" name="request_id" value="' . $request_id . '">';
                    echo '<input type="hidden" name="action" value="accept">';
                    echo '<button type="submit" class="btn accept-btn">Accept</button>';
                    echo '</form>';
                    
                    // Reject Form
                    echo '<form method="POST" action="php/handle_request.php" style="display:inline; margin-left: 10px;">';
                    echo '<input type="hidden" name="request_id" value="' . $request_id . '">';
                    echo '<input type="hidden" name="action" value="reject">';
                    echo '<button type="submit" class="btn reject-btn">Reject</button>';
                    echo '</form>';
                    
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p class='no-requests'>No pending requests for your books.</p>";
            }
            $stmt_received->close();
        }
        ?>
    </div>

    <div class="request-list-container">
        <h3>Requests I've Sent</h3>
        <?php
        $sql_sent = "SELECT r.request_id, b.title AS book_title, u.fullname AS owner_name, r.status, r.requested_at
                     FROM exchange_requests r
                     JOIN books b ON r.requested_book_id = b.id
                     JOIN users u ON r.owner_user_id = u.id
                     WHERE r.requester_user_id = ? 
                     ORDER BY r.requested_at DESC";

        if ($stmt_sent = $conn->prepare($sql_sent)) {
            $stmt_sent->bind_param("i", $current_user_id);
            $stmt_sent->execute();
            $result_sent = $stmt_sent->get_result();

            if ($result_sent->num_rows > 0) {
                while ($row = $result_sent->fetch_assoc()) {
                    $book_title = htmlspecialchars($row['book_title']);
                    $owner_name = htmlspecialchars($row['owner_name']);
                    $status = htmlspecialchars($row['status']);
                    $requested_at = date('M j, Y', strtotime($row['requested_at']));
                    
                    echo '<div class="request-card sent">';
                    echo '<h4>Book Requested: ' . $book_title . '</h4>';
                    echo '<p>From: <strong>' . $owner_name . '</strong></p>';
                    echo '<p>Status: <span class="status-badge status-' . $status . '">' . ucfirst($status) . '</span></p>';
                    echo '<p>Requested On: ' . $requested_at . '</p>';
                    echo '</div>';
                }
            } else {
                echo "<p class='no-requests'>You haven't sent any exchange requests yet.</p>";
            }
            $stmt_sent->close();
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