<?php
    session_start();
    $_SESSION = array();  // Destroy session variables
    session_destroy();    // Destroy the session itself
    setcookie('PHPSESSID', '', time()-300, '/', '', 0); // Destroy cookie

    $url = "/catan2/index.php";
    header("Location: $url");
    exit;
?>
