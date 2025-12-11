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


include 'header.php'; 


?>

<section class="requests-section">
    <h2>Exchange Requests</h2>

    <div class="requests-block">
        <h3>Received Requests</h3>
        <div class="request-list-container">
            <?php
            $sql_received = "SELECT er.request_id, er.requested_book_id, er.status, er.requested_at, 
                             b.title, b.author, b.condition_status AS book_condition,
                             u.fullname AS requester_name, u.email AS requester_email
                             FROM exchange_requests er
                             JOIN books b ON er.requested_book_id = b.id
                             JOIN users u ON er.requester_user_id = u.id
                             WHERE er.owner_user_id = ?
                             ORDER BY er.requested_at DESC";
            
            if ($stmt_received = $conn->prepare($sql_received)) {
                $stmt_received->bind_param("i", $current_user_id);
                $stmt_received->execute();
                $result_received = $stmt_received->get_result();

                if ($result_received->num_rows > 0) {
                    while ($row = $result_received->fetch_assoc()) {
                        $request_id = $row['request_id'];
                        $book_title = htmlspecialchars($row['title']);
                        $requester_name = htmlspecialchars($row['requester_name']);
                        $requester_email = htmlspecialchars($row['requester_email']);
                        $status = htmlspecialchars($row['status']);
                        $requested_at = date("M d, Y H:i", strtotime($row['requested_at']));
                        $book_condition = htmlspecialchars($row['book_condition']);
                        
                        $status_class = strtolower($status);

                        echo '<div class="request-card ' . $status_class . '">';
                        echo '<div class="request-main-info">';
                        echo '<h4>Request for: "' . $book_title . '" (' . $book_condition . ')</h4>';
                        echo '<p class="request-detail">From: <strong>' . $requester_name . '</strong></p>';
                        echo '<p class="request-detail">Status: <span class="status-badge ' . $status_class . '">' . ucfirst($status) . '</span></p>';
                        echo '<p class="request-detail">Requested On: ' . $requested_at . '</p>';

                        if ($status === 'accepted') {
                            echo '<p class="contact-info">Requester Email: <a href="mailto:' . $requester_email . '">' . $requester_email . '</a></p>';
                        }
                        
                        echo '</div>'; 
                        
                        echo '<div class="request-actions">';
                        
                        if ($status === 'pending') {
                            // ACCEPT form
                            echo '<form class="request-action-form" style="display:inline;">';
                            echo '<input type="hidden" name="request_id" value="' . $request_id . '">';
                            echo '<button type="button" class="btn accept-btn" data-action="accept">Accept</button>';
                            echo '</form>';
                            
                            // REJECT form
                            echo '<form class="request-action-form" style="display:inline; margin-left: 10px;">';
                            echo '<input type="hidden" name="request_id" value="' . $request_id . '">';
                            echo '<button type="button" class="btn reject-btn" data-action="reject">Reject</button>';
                            echo '</form>';
                            
                        } else {
                            echo '<button class="btn owner-btn" disabled>' . ucfirst($status) . '</button>';
                        }
                        
                        echo '</div>';
                        echo '</div>'; 
                    }
                } else {
                    echo "<p class='no-requests'>You have no pending or past exchange requests.</p>";
                }
                $stmt_received->close();
            }
            ?>
        </div>
    </div>
    
    <div class="requests-block">
        <h3>Sent Requests</h3>
        <div class="request-list-container">
            <?php
            $sql_sent = "SELECT er.request_id, er.status, er.requested_at, 
                         b.title, b.author, b.condition_status AS book_condition, 
                         u.fullname AS owner_name, u.email AS owner_email
                         FROM exchange_requests er
                         JOIN books b ON er.requested_book_id = b.id
                         JOIN users u ON er.owner_user_id = u.id
                         WHERE er.requester_user_id = ?
                         ORDER BY er.requested_at DESC";

            if ($stmt_sent = $conn->prepare($sql_sent)) {
                $stmt_sent->bind_param("i", $current_user_id);
                $stmt_sent->execute();
                $result_sent = $stmt_sent->get_result();

                if ($result_sent->num_rows > 0) {
                    while ($row = $result_sent->fetch_assoc()) {
                        $request_id = $row['request_id'];
                        $book_title = htmlspecialchars($row['title']);
                        $owner_name = htmlspecialchars($row['owner_name']);
                        $owner_email = htmlspecialchars($row['owner_email']);
                        $status = htmlspecialchars($row['status']);
                        $requested_at = date("M d, Y H:i", strtotime($row['requested_at']));
                        $book_condition = htmlspecialchars($row['book_condition']);

                        $status_class = strtolower($status);
                        
                        echo '<div class="request-card ' . $status_class . '">';
                        echo '<div class="request-main-info">';
                        echo '<h4>Request for: "' . $book_title . '" (' . $book_condition . ')</h4>';
                        echo '<p class="request-detail">To: <strong>' . $owner_name . '</strong></p>';
                        echo '<p class="request-detail">Status: <span class="status-badge ' . $status_class . '">' . ucfirst($status) . '</span></p>';
                        echo '<p class="request-detail">Requested On: ' . $requested_at . '</p>';

                        if ($status === 'accepted') {
                            echo '<p class="contact-info">Owner Email: <a href="mailto:' . $owner_email . '">' . $owner_email . '</a></p>';
                        }

                        echo '</div>'; 
                        
                        echo '<div class="request-actions">';
                        echo '<button class="btn owner-btn" disabled>' . ucfirst($status) . '</button>';
                        echo '</div>'; 

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
    </div>
</section>

<footer>
    <p>© 2025 Community Book Exchange | Created by Shabnam</p>
</footer>
<script src="assets/js/scripts.js"></script>
</body>
</html>