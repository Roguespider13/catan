<?php
    require_once 'force_authentication.php';
    require_once 'gameManager.php';

	$url = "/catan2/php/main.php";
	try {
		$gm = new GameManager();
	}
	catch (Exception $e) {
		$_SESSION['ERROR'] = $e->getMessage();
		header("Location: $url");
		exit;
	}

	if ($gm->isGame($_SESSION['GAMEID']) === TRUE) {
		try {
			// The second player has joined the game, rebuild the game from XML.
			$game = new Game();
			$game->resumeGame($_SESSION['GAMEID']);
			$_SESSION['STATUS'] = $game->getGameState();
			$_SESSION['TURN'] = $game->getPlayersTurn();
			$_SESSION['INIT_BUILD'] = $game->getInitialTurn();
                        $_SESSION['FLOW'] = "WAIT";

			$x = $_GET['x'];
			$y = $_GET['y'];
			$bp = $_GET['bp'];

			// Validate that the coordinates and build position are valid
			if (!is_numeric($x) || !is_numeric($y))
				throw new Exception("Outside game board boundaries.");

			if (!is_string($bp))
				throw new Exception("Cannot build there");

			switch ($_SESSION['STATUS']) {
                            case "Initial":
                                if ($_SESSION['INIT_BUILD'] === "Road") {
                                    $game->buildRoad($_SESSION['TURN'], $x, $y, $bp);
                                }
                                else {
                                    throw new Exception("You cannot build a road right now.");
                                }

                                break;
                            case "Ongoing":
                                // try to execute player options
                                $board = $game->getGameBoard();
                                /* @var $tile BoardTile */
                                $tile = $board[$x][$y];
                                $road = $tile->getRoad($bp);
                                
                                $game->buildRoad($_SESSION['TURN'], $x, $y, $bp);
                                break;
                            case 1:
                                // Do nothing
                                break;
			}
                        //header("Location: $url");
			//exit;
			
		}
		catch (Exception $e) {
                    $_SESSION['ERROR'] = $e->getMessage();
                    echo ">{$_SESSION['ERROR']}<<br />";
                    //header("Location: $url");
                    //exit;
		}
	}
    header("Location: $url");
    exit;
?>
