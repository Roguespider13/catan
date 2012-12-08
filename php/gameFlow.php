<?php
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
                    if ($gm->isGame($_SESSION['GAMEID']) === TRUE  &&
                        $_SESSION['username'] == $_SESSION['TURN']) {
                        $msg .= ", you may:";
                        $msg .= "<ul>";

                        $msg .= "<div>Options</div>";
                        $msg .= "<ul>";
                            $msg .= "<li><a href=\"\">Build Road</a></li>";
                            $msg .= "<li><a href=\"\">Build Settlement</a></li>";
                            $msg .= "<li><a href=\"\">Upgrade to City</a></li>";
                            $msg .= "<li><a href=\"end_turn.php\">End Turn</a></li>";
                        $msg .= "</ul>";
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
    
    echo $msg;
?>