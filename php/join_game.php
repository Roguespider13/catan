<?php
    session_start();
    require_once 'gameManager.php';
    require_once 'Game.php';

    $url = "/catan2/php/create_or_join.php";
    if (isset($_POST['join_game'])) {
        try {
            $gm = new GameManager();
            //$game = new Game();
            $game = $gm->createGame($_POST['game_id'], $_SESSION['username']);
            //$gm->hasWaitingGame($playerID)
            $_SESSION['GAMEID'] = $_POST['game_id'];
            $_SESSION['FLOW'] = "WAIT";
            
            $url = "/catan2/php/main.php";
            header("Location: $url");
            exit;
        }
        catch (Exception $e) {
            $_SESSION['ERROR'] = $e->getMessage();
            //header("Location: $url");
            //exit;
        }
    }
    elseif (isset($_POST['rejoin_game'])) {
        try {
            //echo ">1<<br />";
            //$gm = new GameManager();
            $game = new Game();
            $game->resumeGame($_POST['game_id']);
            $_SESSION['GAMEID'] = $_POST['game_id'];
            $_SESSION['FLOW'] = "WAIT";
            $url = "/catan2/php/main.php";
            //header("Location: $url");
            //exit;
        }
        catch (Exception $e) {
            $_SESSION['ERROR'] = $e->getMessage();
            //header("Location: $url");
            //exit;
        }
    }
    else {
        echo "redirect";
        //header("Location: $url");
        //exit;
    }
?>
