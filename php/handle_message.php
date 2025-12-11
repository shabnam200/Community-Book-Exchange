<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id'], $_POST['message_text'])) {
    
    $request_id = filter_var($_POST['request_id'], FILTER_VALIDATE_INT);
    $sender_id = $_SESSION['id'];
    $message_text = trim($_POST['message_text']);

    if (empty($message_text)) {
        header("location: ../requests.php?error=empty_message");
        exit;
    }

    $stmt_check = $conn->prepare("SELECT requester_user_id, owner_user_id FROM exchange_requests WHERE request_id = ?");
    $stmt_check->bind_param("i", $request_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows === 0) {
        header("location: ../requests.php?error=request_not_found");
        exit;
    }
    
    $request_details = $result_check->fetch_assoc();
    
    if ($sender_id != $request_details['requester_user_id'] && $sender_id != $request_details['owner_user_id']) {
        header("location: ../requests.php?error=unauthorized_message");
        exit;
    }
    $stmt_check->close();

   
    $sql_insert = "INSERT INTO exchange_messages (request_id, sender_id, message_text) VALUES (?, ?, ?)";
    
    if ($stmt_insert = $conn->prepare($sql_insert)) {
        $stmt_insert->bind_param("iis", $request_id, $sender_id, $message_text);
        
        if ($stmt_insert->execute()) {
         
            header("location: ../requests.php?success=message_sent");
        } else {
            header("location: ../requests.php?error=db_error");
        }
        $stmt_insert->close();
    }
    $conn->close();

} else {
    header("location: ../requests.php?error=invalid_submission");
    exit;
}
?>