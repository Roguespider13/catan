<?php
    require_once 'force_authentication.php';
    require_once 'Game.php';
    
    try {
        $game = new Game();
        $game->resumeGame($_SESSION['GAMEID']);
        $game->buildRoad($_SESSION['username'], $x, $y, $buildPosition);
    }
    catch (Exception $e) {
        $_SESSION['ERROR'] = $e->getMessage();
        $url = "/catan2/php/main.php";
        header("Location: $url");
        exit;
    }
?>