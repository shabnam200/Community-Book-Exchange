<?php
session_start();
require_once 'db_config.php'; 

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["id"])) {
    header("location: ../login.html");
    exit;
}

$owner_id = $_SESSION["id"];


$title = trim($_POST['title']);
$author = trim($_POST['author']);
$genre = trim($_POST['genre']);
$condition_status = $_POST['condition'];
$description = trim($_POST['description']);

if (empty($title) || empty($author) || empty($_FILES['cover_image']['name'])) {
    header("location: ../addbook.php?error=missing_fields");
    exit;
}

$upload_dir = '../assets/book_covers/'; 
$file_name = basename($_FILES['cover_image']['name']);
$image_file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
$new_file_name = uniqid() . '.' . $image_file_type; 
$target_file = $upload_dir . $new_file_name;


if(getimagesize($_FILES["cover_image"]["tmp_name"]) === false) {
    header("location: ../addbook.php?error=not_an_image");
    exit;
}

if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
    $cover_image_path = 'assets/book_covers/' . $new_file_name; 
} else {
    header("location: ../addbook.php?error=upload_failed");
    exit;
}

$sql = "INSERT INTO books (title, author, genre, condition_status, description, cover_image, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
 
if($stmt = $conn->prepare($sql)){
    $stmt->bind_param("ssssssi", $title, $author, $genre, $condition_status, $description, $cover_image_path, $owner_id);
    
    if($stmt->execute()){
        header("location: ../books.php?success=book_added");
        exit;
    } else {
        unlink($target_file); 
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>