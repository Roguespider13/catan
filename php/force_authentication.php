<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        $url = "/catan2/index.php";
        header("Location: $url");
        exit;
    }
?>
