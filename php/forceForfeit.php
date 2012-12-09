<?php
    require_once 'force_authentication.php';
    require_once 'Game.php';
    
    try {
        $game = new Game();
        $game->resumeGame($_SESSION['GAMEID']);
        $game->forceForfeit();
    }
    catch (GameOverException $e) {
        $_SESSION['ERROR'] = $e->getMessage();
    }
    catch (Exception $e) {
        $_SESSION['ERROR'] = $e->getMessage();
    }
    
    $url = "/catan2/php/main.php";
    header("Location: $url");
    exit;
?>
