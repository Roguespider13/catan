<?php
    session_start();
    require_once 'gameManager.php';
    require_once 'Game.php';

    $url = "/catan2/index.php";
    if (isset($_POST['join_game'])) {
        try {
            $gm = new GameManager();
            $game = new Game();
            $game = $gm->createGame($_POST['game_id'], $_SESSION['username']);
            $_SESSION['GAMEID'] = $_POST['game_id'];
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
        header("Location: $url");
        exit;
    }
?>
