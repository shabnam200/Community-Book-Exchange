<?php
session_start();
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
<li><a href="index.php" class="active">Home</a></li>
<li><a href="books.php">Books</a></li>
<li><a href="addbook.php">Add Book</a></li>
<li><a href="requests.php">Requests</a></li>
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

<div class="content">
<section class="hero">
<h2>Share Stories, Swap Books, Connect People</h2>
<p>Join your community of book lovers — give a book, take a book, and make new friends along the way!</p>
<a href="register.html" class="btn">Join Now</a>
</section>
</div>

<footer>
<p>© 2025 Community Book Exchange | Created by Shabnam</p>
</footer>
<script src="assets/js/scripts.js"></script>
</body>
</html>