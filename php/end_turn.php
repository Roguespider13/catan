<?php
    require_once 'force_authentication.php';
    require_once 'Game.php';
    
    try {
        $game = new Game();
        $game->resumeGame($_SESSION['GAMEID']);
        if ($game->endPlayersTurn() === TRUE) {
            $_SESSION['STATUS'] = $game->getGameState();
            $_SESSION['TURN'] = $game->getPlayersTurn();
            unset($_SESSION['DIE1']);
            unset($_SESSION['DIE2']);
            $_SESSION['FLOW'] = "WAIT";
            //$_SESSION['INIT_BUILD'] = $game->getInitialTurn();
        }
        else {
            // Do nothing?
        }
    }
    catch (Exception $e) {
        $_SESSION['ERROR'] = $e->getMessage();
    }
    
    $url = "/catan2/php/main.php";
    header("Location: $url");
    exit;
?>