<!DOCTYPE html>
<?php
    require_once 'force_authentication.php';
    require_once 'gameManager.php';
    
    $gameMan = new GameManager();
    $waitingGameId = $gameMan->hasWaitingGame($_SESSION['username']);
    $openGameId = $gameMan->hasOngoingGame($_SESSION['username']);
    
    if ($waitingGameId !== "") {
        $_SESSION['GAMEID'] = $waitingGameId;
        $url = "/catan2/php/main.php";
        header("Location: $url");
        exit;
    }
    
    if ($openGameId !== "") {
        $_SESSION['GAMEID'] = $openGameId;
        $url = "/catan2/php/main.php";
        header("Location: $url");
        exit;
    }
?>
<html>
<head>
    <title>eCatan - Simple Settlers</title>
    <meta http-equiv="refresh" content="10" >
    <link rel="shortcut icon" href="favicon.ico">
    <link rel = "stylesheet" type = "text/css" href = "../css/bootstrap.css" media = "all">
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

        .table-create-join {
            max-width: 300px;
            padding: 19px 29px 29px;
            margin: 20px auto 20px;
            background-color: #fff;
            border: 1px solid #e5e5e5;
            -webkit-border-radius: 5px;
               -moz-border-radius: 5px;
                    border-radius: 5px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
               -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                    box-shadow: 0 1px 2px rgba(0,0,0,.05);
        }

        thead {
            font-size: 2em;
        }

        .table-create-join-heading {
            padding-top: 20px;
        }

        td {
            padding: 15px;
        }
    </style>
</head>

<body>
    <?php
        // Clear all necessary session variables in case the player navigated
        // away from a game to start or join a new one.
        unset($_SESSION['ERROR']);
        
        if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] === "GAMEOVER") {
            unset($_SESSION['GAMEID']);
        }
        
        //
        //unset($_SESSION['STATUS']);
        unset($_SESSION['TURN']);
        unset($_SESSION['INIT_BUILD']);
        unset($_SESSION['FLOW']);
        unset($_SESSION['DIE1']);
        unset($_SESSION['DIE1']);
    ?>
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
              <li class="active"><a href="#">Home</a></li>
              <li><a href="logFiles.php">Logs</a></li>
              <li><a href="stats.php">Stats</a></li>
              <li><a href="signout.php">Signout</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div id="container-fluid">

        <div class="row-fluid">

            <table class="table-create-join">
                <thead>
                    <tr>
                        <?php
                            if (isset($_SESSION['ERROR'])) {
                                echo htmlentities($_SESSION['ERROR']);
                                unset($_SESSION['ERROR']);
                            }

                            echo "<th class=\"table-create-join-heading\" colspan=\"2\">Welcome, " . $_SESSION['username'] . "!</th>";
                        ?>
                    </tr>
                </thead>
                <tr>
                    <td>
                        <?php
                            if (isset($_SESSION['GAMEID'])) {
                                echo "&nbsp;";
                            }
                            else {
                                echo "<form method=\"post\" action=\"create_game.php\">";
                                    echo "<input class=\"btn btn-primary\" type=\"submit\" value=\"Create Game\" name=\"create\">";
                                echo "</form>";
                            }
                        ?>
                    </td>
                    <td>
                        <form method="post" action="join_game.php">
                            <?php
                                if (isset($_SESSION['GAMEID'])) {
                                    echo "<div>";
                                        echo "<input type=\"radio\" name=\"game_id\" value=\"{$_SESSION['GAMEID']}\" />{$_SESSION['GAMEID']}<br />";
                                    echo "</div>";
                                    echo "<input class=\"btn btn-primary\" type=\"submit\" value=\"Rejoin Game\" name=\"rejoin_game\">";
                                }
                                else {
                                    echo "<div>";
                                        $gm = new GameManager();
                                        $gameList = $gm->getOpenGames();

                                        foreach ($gameList as $game) {
                                            echo "<input type=\"radio\" name=\"game_id\" value=\"$game\" />$game<br />";
                                        }
                                    echo "</div>";
                                    
                                    if (count($gameList) > 0) {
                                        echo "<input class=\"btn btn-primary\" type=\"submit\" value=\"Join Game\" name=\"join_game\">";
                                    }
                                    else {
                                        echo "No games to join.";
                                    }
                                }
                            ?>
                        </form>
                    </td>
                </tr>
            </table>
		</div>
	</div>
</body>
</html>