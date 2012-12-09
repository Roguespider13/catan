<?php
    require_once 'force_authentication.php';
    
    // Redirect the user back to the create or join page if no games have been started
    if (isset($_SESSION['GAMEID'])) {
        $url = "/catan2/php/main.php";
    }
    else {
        $url = "/catan2/php/create_or_join.php";
    }
    
    header("Location: $url");
    exit;
?>
