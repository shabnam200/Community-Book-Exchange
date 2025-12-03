<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id'], $_POST['action'])) {
    
    $request_id = filter_var($_POST['request_id'], FILTER_VALIDATE_INT);
    $action = $_POST['action'];
    $user_id = $_SESSION['id'];
    
   
    if ($action === 'accept') {
        $new_status = 'accepted';
    } elseif ($action === 'reject') {
        $new_status = 'rejected';
    } else {
        header("location: ../requests.php?error=invalid_action");
        exit;
    }

    $stmt = $conn->prepare("SELECT owner_user_id FROM exchange_requests WHERE request_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("location: ../requests.php?error=request_not_found");
        exit;
    }
    
    $request_data = $result->fetch_assoc();
    $owner_id = $request_data['owner_user_id'];
    $stmt->close();

    if ($owner_id != $user_id) {
        header("location: ../requests.php?error=unauthorized");
        exit;
    }

    $stmt = $conn->prepare("UPDATE exchange_requests SET status = ? WHERE request_id = ?");
    $stmt->bind_param("si", $new_status, $request_id);
    
    if ($stmt->execute()) {
        header("location: ../requests.php?success=" . $new_status);
    } else {
        header("location: ../requests.php?error=db_update_failed");
    }
    
    $stmt->close();
    $conn->close();
    exit;
} else {
    header("location: ../requests.php");
    exit;
}
?>