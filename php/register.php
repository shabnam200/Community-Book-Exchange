<?php
session_start();
require_once 'db_config.php'; 

$fullname = trim($_POST['fullname']);
$email = trim($_POST['email']);
$password = $_POST['password'];

if (empty($fullname) || empty($email) || empty($password)) {
    header("location: ../register.html?error=missing_fields");
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql_check = "SELECT id FROM users WHERE email = ?";
if($stmt_check = $conn->prepare($sql_check)){
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if($stmt_check->num_rows == 1){
        header("location: ../register.html?error=email_exists");
        exit;
    }
    $stmt_check->close();
}

$sql_insert = "INSERT INTO users (fullname, email, password_hash) VALUES (?, ?, ?)";
 
if($stmt_insert = $conn->prepare($sql_insert)){
    $stmt_insert->bind_param("sss", $fullname, $email, $password_hash);
    
    if($stmt_insert->execute()){
        header("location: ../login.html?success=registration_successful");
        exit;
    } else {
        header("location: ../register.html?error=db_error");
        exit;
    }

    $stmt_insert->close();
}

$conn->close();
?>