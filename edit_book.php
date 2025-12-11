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

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("location: mybooks.php?error=invalid_book_id");
    exit;
}

$book_id = $_GET['id'];
$book_data = null;
$error = '';
$success = '';
$sql_fetch = "SELECT id, title, author, condition_status, cover_image, description, is_available 
              FROM books 
              WHERE id = ? AND owner_id = ?";

if ($stmt_fetch = $conn->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("ii", $book_id, $current_user_id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();

    if ($result_fetch->num_rows === 1) {
        $book_data = $result_fetch->fetch_assoc();
    } else {
        
        header("location: mybooks.php?error=unauthorized_or_not_found");
        exit;
    }
    $stmt_fetch->close();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_book'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $condition = $_POST['condition_status'];
    $description = trim($_POST['description']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    $current_image_path = $book_data['cover_image']; 
    $new_image_uploaded = false;
    
  
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['cover_image']['tmp_name'];
        $file_name = $_FILES['cover_image']['name'];
        $file_size = $_FILES['cover_image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpeg', 'jpg', 'png'];

        if (in_array($file_ext, $allowed_extensions)) {
            if ($file_size < 5000000) { 
                $new_file_name = uniqid('book_') . '.' . $file_ext;
                $upload_dir = 'uploads/covers/';
                $upload_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $current_image_path = $upload_path;
                    $new_image_uploaded = true;
                    if ($book_data['cover_image'] != 'assets/images/default.jpg' && file_exists($book_data['cover_image'])) {
                        unlink($book_data['cover_image']);
                    }
                } else {
                    $error = "Failed to move uploaded file.";
                }
            } else {
                $error = "Image size must be less than 5MB.";
            }
        } else {
            $error = "Only JPG, JPEG, and PNG files are allowed.";
        }
    }

    if (empty($error)) {
        $sql_update = "UPDATE books SET title = ?, author = ?, condition_status = ?, description = ?, cover_image = ?, is_available = ? WHERE id = ? AND owner_id = ?";
        
        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("sssssiii", $title, $author, $condition, $description, $current_image_path, $is_available, $book_id, $current_user_id);
            
            if ($stmt_update->execute()) {
                $success = "Book details updated successfully!";
                $book_data['title'] = $title;
                $book_data['author'] = $author;
                $book_data['condition_status'] = $condition;
                $book_data['description'] = $description;
                $book_data['is_available'] = $is_available;
                $book_data['cover_image'] = $current_image_path;
            } else {
                $error = "Database error: " . $conn->error;
            }
            $stmt_update->close();
        }
    }
}


include 'header.php';
?>

<section class="form-container">
    <h2>Edit Book Details</h2>

    <?php if ($success): ?>
        <p class="success-msg"><?php echo $success; ?></p>
    <?php elseif ($error): ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php elseif (isset($_GET['success']) && $_GET['success'] === 'updated'): ?>
        <p class="success-msg">Book successfully updated!</p>
    <?php endif; ?>

    <?php if ($book_data): ?>
        <form class="book-form" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="book_id" value="<?php echo $book_data['id']; ?>">
            
            <label for="title">Book Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book_data['title']); ?>" required>

            <label for="author">Author</label>
            <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($book_data['author']); ?>" required>

            <label for="condition_status">Condition</label>
            <select id="condition_status" name="condition_status" required>
                <?php
                $conditions = ['New', 'Like New', 'Good', 'Used', 'Poor'];
                foreach ($conditions as $cond) {
                    $selected = ($book_data['condition_status'] === $cond) ? 'selected' : '';
                    echo "<option value=\"$cond\" $selected>$cond</option>";
                }
                ?>
            </select>

            <label for="description">Description (Optional)</label>
            <textarea id="description" name="description" placeholder="Any specific notes about this copy..."><?php echo htmlspecialchars($book_data['description']); ?></textarea>
            
            <div class="current-image-preview" style="text-align: center; margin: 15px 0;">
                <p>Current Cover:</p>
                <img src="<?php echo htmlspecialchars($book_data['cover_image']); ?>" alt="Current Cover" style="max-width: 150px; height: auto; border: 1px solid #ccc;">
            </div>

            <label for="cover_image">Replace Cover Image (Optional)</label>
            <input type="file" id="cover_image" name="cover_image" accept="image/*">
            
            <div style="text-align: left; margin-top: 15px;">
                <input type="checkbox" id="is_available" name="is_available" value="1" <?php echo $book_data['is_available'] ? 'checked' : ''; ?>>
                <label for="is_available" style="display: inline; font-weight: bold;">Available for Exchange</label>
                <p style="font-size: 0.85em; color: #666; margin-top: 5px;">Uncheck this if you want to temporarily remove this specific copy from the listings.</p>
            </div>

            <button type="submit" name="update_book" class="btn submit-btn">Save Changes</button>
            <a href="mybooks.php" class="btn reject-btn" style="background-color: #555; text-align: center;">Cancel / Back to My Books</a>
        </form>
    <?php else: ?>
        <p class="error-msg">Error: Book data could not be loaded. Please ensure you are logged in and own this book.</p>
        <a href="mybooks.php" class="btn primary-btn">Go to My Books</a>
    <?php endif; ?>
</section>

<footer>
    <p>© 2025 Community Book Exchange | Created by Shabnam</p>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>