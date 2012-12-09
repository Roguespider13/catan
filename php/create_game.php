<?php
    session_start();
    require_once 'gameManager.php';

    $url = "/catan2/create_or_join.php";
    if (isset($_POST['create'])) {
        try {
            $manager = new GameManager();
            $_SESSION['GAMEID'] = $manager->createEmptyGame($_SESSION['username']);
            $_SESSION['TURN'] = $_SESSION['username'];
            $_SESSION['FLOW'] = "";
            
            $url = "/catan2/php/main.php";
            header("Location: $url");
            exit;
        }
        catch (Exception $e) {
            $_SESSION['ERROR'] = $e->getMessage();
            header("Location: $url");
            exit;
        }
    }
    else {
        $_SESSION['ERROR'] = "INVALID SCRIPT ENTRY";
        header("Location: $url");
        exit;
    }
?>
