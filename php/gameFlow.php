<?php
    $msg = "";
    if ($_SESSION['STATUS'] !== "GAMEOVER") {
        $msg = $_SESSION['TURN'];
        switch ($_SESSION['STATUS']) {
            case "WAIT":
                $msg = "Waiting for second player...";
                break;
            case "Initial":
                if ($_SESSION['INIT_BUILD'] === "Settlement")
                    $msg .= ", please place your settlement.";
                else
                    $msg .= ", please place your road.";

                break;
            case "Ongoing":
                switch ($_SESSION['FLOW']) {
                    case "OPTIONS":
                        // $_SESSION['username'] == $_SESSION['TURN']
                        if ($gm->isGame($_SESSION['GAMEID']) === TRUE) {
                            $msg .= ", you may:";
                            $msg .= "<ul>";
                            $msg .= "<div>Options</div>";
                            $msg .= "<ul>";
                                $msg .= "<li>Build Road</li>";
                                $msg .= "<li>Build Settlement</li>";
                                $msg .= "<li>Upgrade to City</li>";
                                
                                if (isset($_SESSION['DIE1']) && isset($_SESSION['DIE2'])) {
                                    $msg .= "<li><a href=\"end_turn.php\">End Turn</a></li>";
                                }
                            $msg .= "</ul><br /><br />";

                            $msg .= "Your resources:<br /><ul>";
                            if (isset($cards)) {
                                foreach ($cards as $key => $value) {
                                    $msg .= "<li>" . ucwords($key) . ": " . $value . "</li>";
                                }
                            }
                            $msg .= "</ul>";
                        }

                        break;
                    case "WAIT":
                        $msg = "Wait for opponent to complete turn.<br /><br /><br />";
                        
                        $msg .= "Your resources:<br /><ul>";
                        if (isset($cards)) {
                            foreach ($cards as $key => $value) {
                                $msg .= "<li>" . ucwords($key) . ": " . $value . "</li>";
                            }
                        }
                        $msg .= "</ul>";
                        
                        /* @var $game Game */

                        if ($game->canForceForfeit() === TRUE) {
                            $msg .= "Your opponent appears to not be responding.  Click <a href=\"forceForfeit.php\">here</a> to force opponent forfeit.";
                        }
                        break;
                    default:
                        $msg .= ", please roll.";
                        break;
                }

            case 1:
                // Do nothing
                break;
        }
    }
    echo $msg;
?>