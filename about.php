<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'php/db_config.php';

$pending_count = 0;
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $current_user_id = $_SESSION["id"];

    $sql_count = "SELECT COUNT(*) AS pending_count FROM exchange_requests WHERE owner_user_id = ? AND status = 'pending'";

    if ($stmt_count = $conn->prepare($sql_count)) {
        $stmt_count->bind_param("i", $current_user_id);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $row_count = $result_count->fetch_assoc();
        $pending_count = $row_count['pending_count'];
        $stmt_count->close();
    }
}

include 'header.php';
?>

<div class="content">
    <section class="about-section">
      <h2>Our Mission</h2>
      
      <p>
        The Community Book Exchange platform was created to foster a simple, sustainable way for book lovers to share their stories and discover new ones. We believe every book deserves a new reader and every reader deserves a new adventure without the cost.
      </p>

      <h3>How It Works</h3>
      <ul>
        <li>**List Your Books:** Easily add the books you are ready to part with using the "Add Book" form.</li>
        <li>**Browse & Request:** Look through books listed by other members of your community.</li>
        <li>**Exchange:** Arrange a meeting or mailing swap with the other member to exchange your books.</li>
      </ul>

      <h3>Meet the Creator</h3>
      <p>
        This project was created by Shabnam as a passion project to connect local book communities. We are committed to making reading accessible and promoting sustainable consumption.
      </p>

    </section>
  </div>

  <footer>
    <p>© 2025 Community Book Exchange | Created by Shabnam</p>
  </footer>

  <script src="assets/js/main.js"></script>
</body>
</html>