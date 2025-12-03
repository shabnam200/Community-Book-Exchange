<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_id'])) {
    
    $requested_book_id = filter_var($_POST['book_id'], FILTER_VALIDATE_INT);
    $requester_user_id = $_SESSION['id'];
    
    if (!$requested_book_id) {
        header("location: ../books.php?msg=invalid_book");
        exit;
    }
    
    $stmt = $conn->prepare("SELECT owner_id FROM books WHERE id = ?");
    $stmt->bind_param("i", $requested_book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        header("location: ../books.php?msg=book_not_found");
        exit;
    }
    
    $book_data = $result->fetch_assoc();
    $owner_user_id = $book_data['owner_id'];
    $stmt->close();
    
    if ($requester_user_id == $owner_user_id) {
        header("location: ../books.php?msg=cannot_exchange_own_book");
        exit;
    }

    $stmt = $conn->prepare("SELECT request_id FROM exchange_requests WHERE requested_book_id = ? AND requester_user_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $requested_book_id, $requester_user_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        header("location: ../books.php?msg=already_requested");
        exit;
    }
    $stmt->close();
    
    $stmt = $conn->prepare("INSERT INTO exchange_requests (requested_book_id, requester_user_id, owner_user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $requested_book_id, $requester_user_id, $owner_user_id);
    
    if ($stmt->execute()) {
        header("location: ../books.php?msg=request_sent");
    } else {
        header("location: ../books.php?msg=request_failed");
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("location: ../books.php");
    exit;
}
?>