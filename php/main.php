<!DOCTYPE html>
<?php
    require_once 'force_authentication.php';
    
    // Redirect the user back to the create or join page if no games have been started
    if (!isset($_SESSION['GAMEID'])) {
        $url = "/catan2/php/create_or_join.php";
        header("Location: $url");
        exit;
    }
    
    require_once 'gameManager.php';
?>
<html>
    <head>
		<title>eCatan</title>
        <meta http-equiv="refresh" content="10" >
        <link rel = "stylesheet" type = "text/css" href = "../css/bootstrap.css" media = "all">
        <link rel = "stylesheet" type = "text/css" href = "../newTest.css" media = "all">
        <link rel = "stylesheet" type = "text/css" href = "../settlements.css" media = "all">
        <link href='http://fonts.googleapis.com/css?family=Carrois+Gothic'
			rel='stylesheet' type='text/css'>
           <style type="text/css">

        body {
            padding-top: 60px;
            padding-bottom: 40px;
            background:url("../images/bg.png");
        }

        .navbar {
            box-shadow:0 2px 3px #b1b4e7;
        }

        span {
            font-size: 2em;
            left:-35%;
            margin-top:0;
            color: #FF7373;
            position: relative;
        }

        /*table img {
            padding-left: 30px;
            padding-top: 5px;
            padding-bottom: 5px;
        }*/

    </style>
</head>

<body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">eCatan</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="create_or_join.php">Home</a></li>
              <li><a href="logFiles.php">Logs</a></li>
              <li><a href="stats.php">Stats</a></li>
              <li><a href="signout.php">Signout</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

            <?php
                $gm = new GameManager();
                $game = new Game();
                try {
                    if ($gm->isGame($_SESSION['GAMEID']) === TRUE) {
                        // The second player has joined the game, rebuild the game from XML.

                        $game->resumeGame($_SESSION['GAMEID']);

                        // Get the game state/status and the turn values
                        $_SESSION['STATUS'] = $game->getGameState();
                        $_SESSION['TURN'] = $game->getPlayersTurn();
                        $_SESSION['INIT_BUILD'] = $game->getInitialTurn();
                        //$player = new Player($_SESSION['username']);
                        $player = $game->getPlayer($_SESSION['username']);
                        $cards = $player->getCardArray();
                    }
                    else {
                        // Still waiting for a second player.
                        $_SESSION['STATUS'] = "WAIT";
                        $_SESSION['TURN'] = NULL;
                        $_SESSION['FLOW'] = "";
                    }
                }
                catch (GameOverException $e) {
                    $_SESSION['ERROR'] = $e->getErrorMessage();
                    $_SESSION['STATUS'] = "GAMEOVER";
                    $_SESSION['TURN'] = "";
                }
                catch (Exception $e) {
                    $_SESSION['ERROR'] = $e->getMessage();
                }
                
                if (isset($_SESSION['ERROR'])) {
                    echo "<div class=\"gameOver\">" . htmlentities($_SESSION['ERROR']) . "</div><br />";
                    unset($_SESSION['ERROR']);
                }
                
                if ($_SESSION['username'] == $_SESSION['TURN']) {
                    $_SESSION['FLOW'] = "OPTIONS";
                }

                //echo "Welcome, " . $_SESSION['username'] . "!";
                //echo "&nbsp;&nbsp;Game ID: " . $_SESSION['GAMEID'];
                //echo "<br />Status: " . $_SESSION['STATUS'];
                //echo "<br />Turn: " . $_SESSION['TURN'];
                //echo "<br />Flow: " . $_SESSION['FLOW'];
            ?>
		</nav>
		<div id = "left-sidebar">
                    <div>Dice-rolling section</div>
                    <?php
                        if ($_SESSION['STATUS'] === "Ongoing" &&
                            !isset($_SESSION['DIE1']) &&
                            !isset($_SESSION['DIE2']) &&
                            ($_SESSION['username'] == $_SESSION['TURN'])) {
                                echo "<a href=\"roll_dice.php\">Roll dice</a><br />";
                        }

                        if (isset($_SESSION['DIE1']) && isset($_SESSION['DIE2'])) {
                                require_once 'display_dice.php';
                        }
                    ?>
		</div>
        <div id = "right-sidebar">
            <b>Message to players</b><br />
            <?php
                require_once 'gameFlow.php';
            ?>
        </div>
    <?php
        $style = "display: block;";
        if ($gm->isGame($_SESSION['GAMEID']) === TRUE) {
            $style = "display: block;";
        }

        echo "<div id=\"center\" style=\"$style\">";

		// Determine whether or not to allow player to interact with board
		// depending on whose turn it is.
		$settlement = "ni_settlement";
		$hroad = "";
		$vroad = "";
                
		if ($_SESSION['username'] === $_SESSION['TURN']) {
			$settlement = "settlement";
			$hroad = "hroad";
			$vroad = "vroad";
		}

		function setSettlementCoords($x, $y, $bp, $settle) {
                    $block = "<div></div>";
                    if (strlen($settle) > 0) {
                        $block = $settle;
                    }

                    if ($_SESSION['username'] == $_SESSION['TURN']) {
                        return "<a href=\"place_settlement.php?x=$x&y=$y&bp=$bp\">$block</a>";
                    }
                    else {
                        return $block;
                    }
		}

		function setRoadCoords($x, $y, $bp, $road) {
                    if($bp === "top" || $bp === "bottom") {
                        $class = "emptyRoad";
                    }
                    else {

                    }
                    $block = "<div class=\"\"></div>";
                    if (strlen($road) > 0) {
                        $block = $road;
                    }

                    if ($_SESSION['username'] == $_SESSION['TURN']) {
                        return "<a href=\"place_road.php?x=$x&y=$y&bp=$bp\">$block</a>";
                    }
                    else {
                        return $block;
                    }
		}

                static $TOPLEFT = "2";
                static $TOPRIGHT = "3";
                static $BOTTOMLEFT = "4";
                static $BOTTOMRIGHT = "5";

                static $TOP = "6";
                static $RIGHT = "7";
                static $BOTTOM = "8";
                static $LEFT = "9";

                if ($gm->isGame($_SESSION['GAMEID']) === TRUE) {
                    require_once 'game_board.php';

                    echo "<table border=\"0\">";
                    echo "<tr>";
                        echo "<td class=\"$settlement\">";
                            echo setSettlementCoords(0, 0, "topLeft", $tile00[$TOPLEFT]);
                            //echo $tile00[$TOPLEFT];
                        echo "</td>";
                        echo "<td class=\"$hroad\">";
                            echo setRoadCoords(0, 0, "top", $tile00[$TOP]);
                        echo "</td>";
                        echo "<td class=\"$settlement\">";
                            echo setSettlementCoords(0, 0, "topRight", $tile00[$TOPRIGHT]);
                        echo "</td>";
                        echo "<td class=\"$hroad\">";
                            echo setRoadCoords(0, 1, "top", $tile01[$TOP]);
                        echo "</td>";
                        echo "<td class=\"$settlement\">";
                            echo setSettlementCoords(0, 1, "topRight", $tile01[$TOPRIGHT]);
                        echo "</td>";
                        echo "<td class=\"$hroad\">";
                            echo setRoadCoords(0, 2, "top", $tile02[$TOP]);
                        echo "</td>";
                        echo "<td class=\"$settlement\">";
                            echo setSettlementCoords(0, 2, "topRight", $tile02[$TOPRIGHT]);
                        echo "</td>";
                        echo "<td class=\"$hroad\">";
                            echo setRoadCoords(0, 3, "top", $tile03[$TOP]);
                        echo "</td>";
                        echo "<td class=\"$settlement\">";
                            echo setSettlementCoords(0, 3, "topRight", $tile03[$TOPRIGHT]);
                        echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td class=\"$vroad\">";
                            echo setRoadCoords(0, 0, "left", $tile00[$LEFT]);
                        echo "</td>";
                        echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile00[0]) . ".jpg\" />";
                            echo '<span>', htmlentities($tile00[1]), '</span>';
                        echo "</td>";
                        echo "<td class=\"$vroad\">";
                            echo setRoadCoords(0, 0, "right", $tile00[$RIGHT]);
                        echo "</td>";
                        echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile01[0]) . ".jpg\" />";
                            echo '<span>', htmlentities($tile01[1]), '</span>';
                        echo "</td>";
                        echo "<td class=\"$vroad\">";
                            echo setRoadCoords(0, 1, "right", $tile01[$RIGHT]);
                        echo "</td>";
                        echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile02[0]) . ".jpg\" />";
                            echo '<span>', htmlentities($tile02[1]), '</span>';
                        echo "</td>";
                        echo "<td class=\"$vroad\">";
                            echo setRoadCoords(0, 2, "right", $tile02[$RIGHT]);
                        echo "</td>";
                        echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile03[0]) . ".jpg\" />";
                            echo '<span>', htmlentities($tile03[1]), '</span>';
                        echo "</td>";
                        echo "<td class=\"$vroad\">";
                            echo setRoadCoords(0, 3, "right", $tile03[$RIGHT]);
                        echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td class=\"$settlement\">";
                            echo setSettlementCoords(1, 0, "topLeft", $tile10[$TOPLEFT]);
			echo "</td>";
                        echo "<td class=\"$hroad\">";
                            echo setRoadCoords(0, 0, "bottom", $tile00[$BOTTOM]);
			echo "</td>";
                        echo "<td class=\"$settlement\">";
                            echo setSettlementCoords(1, 0, "topRight", $tile10[$TOPRIGHT]);
                        echo "</td>";
                        echo "<td class=\"$hroad\">";
                            echo setRoadCoords(0, 1, "bottom", $tile01[$BOTTOM]);
                        echo "</td>";
                        echo "<td class=\"$settlement\">";
                            echo setSettlementCoords(1, 1, "topRight", $tile11[$TOPRIGHT]);
                        echo "</td>";
                        echo "<td class=\"$hroad\">";
                            echo setRoadCoords(0, 2, "bottom", $tile02[$BOTTOM]);
                        echo "</td>";
                        echo "<td class=\"$settlement\">";
                            echo setSettlementCoords(1, 2, "topRight", $tile12[$TOPRIGHT]);
                        echo "</td>";
                        echo "<td class=\"$hroad\">";
                            echo setRoadCoords(0, 3, "bottom", $tile03[$BOTTOM]);
                        echo "</td>";
                        echo "<td class=\"$settlement\">";
                            echo setSettlementCoords(1, 3, "topRight", $tile13[$TOPRIGHT]);
                        echo "</td>";
                    echo "</tr>";
                echo "<tr>";
                    echo "<td class=\"$vroad\">";
                        echo setRoadCoords(1, 0, "left", $tile10[$LEFT]);
                    echo "</td>";
                    echo "<td>";
                        echo "<img src=\"../images/" . htmlentities($tile10[0]) . ".jpg\" />";
                        echo '<span>', htmlentities($tile10[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(1, 0, "right", $tile10[$RIGHT]);
                    echo "</td>";
                    echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile11[0]) . ".jpg\" />";
                             echo '<span>', htmlentities($tile11[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(1, 1, "right", $tile11[$RIGHT]);
                    echo "</td>";
                    echo "<td>";
                        echo "<img src=\"../images/" . htmlentities($tile12[0]) . ".jpg\" />";
                        echo '<span>', htmlentities($tile12[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(1, 2, "right", $tile12[$RIGHT]);
                    echo "</td>";
                    echo "<td>";
                        echo "<img src=\"../images/" . htmlentities($tile13[0]) . ".jpg\" />";
                        echo '<span>', htmlentities($tile13[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                        echo setRoadCoords(1, 3, "right", $tile13[$RIGHT]);
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(2, 0, "topLeft", $tile20[$TOPLEFT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(1, 0, "bottom", $tile10[$BOTTOM]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(2, 0, "topRight", $tile20[$TOPRIGHT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(1, 1, "bottom", $tile11[$BOTTOM]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(2, 1, "topRight", $tile21[$TOPRIGHT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(1, 2, "bottom", $tile12[$BOTTOM]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(2, 2, "topRight", $tile22[$TOPRIGHT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(1, 3, "bottom", $tile13[$BOTTOM]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(2, 3, "topRight", $tile23[$TOPRIGHT]);
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(2, 0, "left", $tile20[$LEFT]);
                    echo "</td>";
                    echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile20[0]) . ".jpg\" />";
                            echo '<span>', htmlentities($tile20[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(2, 0, "right", $tile20[$RIGHT]);
                    echo "</td>";
                    echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile21[0]) . ".jpg\" />";
                             echo '<span>', htmlentities($tile21[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(2, 1, "right", $tile21[$RIGHT]);
                    echo "</td>";
                    echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile22[0]) . ".jpg\" />";
                             echo '<span>', htmlentities($tile22[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(2, 2, "right", $tile22[$RIGHT]);
                    echo "</td>";
                    echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile23[0]) . ".jpg\" />";
                             echo '<span>', htmlentities($tile23[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(2, 3, "right", $tile23[$RIGHT]);
                    echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(3, 0, "topLeft", $tile30[$TOPLEFT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(3, 0, "top", $tile30[$TOP]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(3, 0, "topRight", $tile30[$TOPRIGHT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(3, 1, "top", $tile31[$TOP]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(3, 1, "topRight", $tile31[$TOPRIGHT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(3, 2, "top", $tile32[$TOP]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(3, 2, "topRight", $tile32[$TOPRIGHT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(3, 3, "top", $tile33[$TOP]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(3, 3, "topRight", $tile33[$TOPRIGHT]);
                    echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(3, 0, "left", $tile30[$LEFT]);
                    echo "</td>";
                    echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile30[0]) . ".jpg\" />";
                             echo '<span>', htmlentities($tile30[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(3, 0, "right", $tile30[$RIGHT]);
                    echo "</td>";
                    echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile31[0]) . ".jpg\" />";
                             echo '<span>', htmlentities($tile31[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(3, 1, "right", $tile31[$RIGHT]);
                    echo "</td>";
                    echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile32[0]) . ".jpg\" />";
                             echo '<span>', htmlentities($tile32[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(3, 2, "right", $tile32[$RIGHT]);
                    echo "</td>";
                    echo "<td>";
                            echo "<img src=\"../images/" . htmlentities($tile33[0]) . ".jpg\" />";
                             echo '<span>', htmlentities($tile33[1]), '</span>';
                    echo "</td>";
                    echo "<td class=\"$vroad\">";
                             echo setRoadCoords(3, 3, "right", $tile33[$RIGHT]);
                    echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(3, 0, "bottomLeft", $tile30[$BOTTOMLEFT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(3, 0, "bottom", $tile30[$BOTTOM]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(3, 0, "bottomRight", $tile30[$BOTTOMRIGHT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(3, 1, "bottom", $tile31[$BOTTOM]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(3, 1, "bottomRight", $tile31[$BOTTOMRIGHT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(3, 2, "bottom", $tile32[$BOTTOM]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(3, 2, "bottomRight", $tile32[$BOTTOMRIGHT]);
                    echo "</td>";
                    echo "<td class=\"$hroad\">";
                             echo setRoadCoords(3, 3, "bottom", $tile33[$BOTTOM]);
                    echo "</td>";
                    echo "<td class=\"$settlement\">";
                             echo setSettlementCoords(3, 3, "bottomRight", $tile33[$BOTTOMRIGHT]);
                    echo "</td>";
                    echo "</tr>";
                echo "</table>";
                }
    ?>
    </div>
	</div>
    </body>
</html>
