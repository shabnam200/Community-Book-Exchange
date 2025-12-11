<?php
if (!isset($current_file)) {
    $current_file = basename($_SERVER['PHP_SELF']);
}
if (!isset($pending_count)) {
    $pending_count = 0; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Community Book Exchange</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <header>
        <nav class="navbar">
            <h1 class="logo">📚 Community Book Exchange</h1>
            <ul class="nav-links">
                <li><a href="index.php" class="<?php echo ($current_file === 'index.php') ? 'active' : ''; ?>">Home</a></li>
                <li><a href="books.php" class="<?php echo ($current_file === 'books.php' || $current_file === 'book_detail.php') ? 'active' : ''; ?>">Books</a></li>
                <li><a href="addbook.php" class="<?php echo ($current_file === 'addbook.php') ? 'active' : ''; ?>">Add Book</a></li>
                <li><a href="mybooks.php" class="<?php echo ($current_file === 'mybooks.php') ? 'active' : ''; ?>">My Books</a></li>
                <li><a href="requests.php" class="<?php echo ($current_file === 'requests.php') ? 'active nav-requests' : 'nav-requests'; ?>">
                    Requests
                    <?php if (isset($_SESSION["loggedin"]) && $pending_count > 0): ?>
                        <span class="notification-badge"><?php echo $pending_count; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="about.php" class="<?php echo ($current_file === 'about.php') ? 'active' : ''; ?>">About</a></li>
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