<?php
    require_once 'force_authentication.php';
    require_once 'Game.php';
    require_once 'gameManager.php';

    try {
        $game = new Game();
        $game->resumeGame($_SESSION['GAMEID']);
        $die1 = $game->performDieRoll();
        $die2 = $game->performDieRoll();

        session_start();
        $_SESSION['DIE1'] = $die1;
        $_SESSION['DIE2'] = $die2;
        $game->setDice($die1, $die2);
        $_SESSION['FLOW'] = "OPTIONS";

        $url = "/catan2/php/main.php";
        header("Location: $url");
        exit;
    }
    catch (Exception $e) {
        $_SESSION['ERROR'] = $e->getMessage();
        $url = "/catan2/php/main.php";
        header("Location: $url");
        exit;
    }
?>
