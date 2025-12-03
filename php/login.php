<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../books.php");
    exit;
}

require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT id, fullname, password_hash FROM users WHERE email = ?";

    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("s", $param_email);
        $param_email = $email;

        if($stmt->execute()){
            $stmt->store_result();

            if($stmt->num_rows == 1){

                $stmt->bind_result($id, $fullname, $hashed_password);
                
                if($stmt->fetch()){
                    if(password_verify($password, $hashed_password)){
                        
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["fullname"] = $fullname;
                        $_SESSION["email"] = $email;
                        
                        header("location: ../books.php");
                        exit;
                    } else {
                        header("location: ../login.html?error=invalid_credentials");
                        exit;
                    }
                }
            } else {
                header("location: ../login.html?error=invalid_credentials");
                exit;
            }
        } else {
            echo "Oops! Something went wrong. Please try again later. " . $conn->error;
        }

        $stmt->close();
    }
    
    $conn->close();
} else {
    header("location: ../login.html");
    exit;
}
?>