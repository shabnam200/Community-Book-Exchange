<?php
session_start();
require_once 'db_config.php';

header('Content-Type: application/json');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(["success" => false, "error" => "Unauthorized access. Please log in."]);
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
        echo json_encode(["success" => false, "error" => "Invalid action specified."]);
        exit;
    }
    
    
    $stmt = $conn->prepare("SELECT owner_user_id, requested_book_id FROM exchange_requests WHERE request_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Request not found."]);
        $stmt->close();
        exit;
    }
    
    $request_data = $result->fetch_assoc();
    $owner_id = $request_data['owner_user_id'];
    $book_id = $request_data['requested_book_id'];
    $stmt->close();


    if ($owner_id != $user_id) {
        echo json_encode(["success" => false, "error" => "Authorization failure. You do not own this request."]);
        exit;
    }

    $conn->begin_transaction();

    try {

        $stmt_update_request = $conn->prepare("UPDATE exchange_requests SET status = ? WHERE request_id = ? AND owner_user_id = ?");
        $stmt_update_request->bind_param("sii", $new_status, $request_id, $user_id);
        
        if (!$stmt_update_request->execute()) {
             throw new Exception("Failed to update request status.");
        }
        $stmt_update_request->close();

        
        if ($new_status === 'accepted') {
            
            $stmt_update_book = $conn->prepare("UPDATE books SET is_available = GREATEST(0, is_available - 1) WHERE id = ? AND owner_id = ?");
            $stmt_update_book->bind_param("ii", $book_id, $user_id);
            
            if (!$stmt_update_book->execute()) {
                throw new Exception("Failed to update book availability.");
            }
            $stmt_update_book->close();
        }

        $conn->commit();
        echo json_encode(["success" => true, "message" => "Request successfully " . $new_status . "."]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "error" => "Database Transaction Failed. Contact support."]);
    }
    
    $conn->close();
    exit;
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
    exit;
}
?>