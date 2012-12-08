<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        $url = "http://localhost/catan2/index.php";
        header("Location: $url");
        exit;
    }
?>
