<?php
	$board = array();
	$board = $game->getGameBoard();
	$tile00 = array(); $tile01 = array(); $tile02 = array(); $tile03 = array();
	$tile10 = array(); $tile11 = array(); $tile12 = array(); $tile13 = array();
	$tile20 = array(); $tile21 = array(); $tile22 = array(); $tile23 = array();
	$tile30 = array(); $tile31 = array(); $tile32 = array(); $tile33 = array();
        
        function determineSettlementTypeAndColor($occupation) {
            $s = "";
            if (strlen($occupation) == 0)
                return "";
            
            $arr = explode(".", $occupation);
            if ($arr[0] == "S") {
                if ($arr[1] == $_SESSION['username']) {
                    $s = "<img src=\"./../images/BlueSettlement.png\" />";
                }
                else {
                    $s = "<img src=\"./../images/OrangeSettlement.png\" />";
                }
            }
            else {
                if ($arr[1] == $_SESSION['username']) {
                    $s = "<img src=\"./../images/BlueCity.png\" />";
                }
                else {
                    $s = "<img src=\"./../images/OrangeCity.png\" />";
                }
            }
            
            return $s;
        }
        
        function determineRoadTypeAndColor($occupation, $alignment) {
            $r = "";
            if (strlen($occupation) == 0)
                return "";

            if ($alignment == "h") {
                if ($occupation == $_SESSION['username']) {
                    $r = "<img src=\"./../images/HorizBlueRoad.png\" />";
                }
                else {
                    $r = "<img src=\"./../images/HorizOrangeRoad.png\" />";
                }
            }
            else {
                if ($occupation == $_SESSION['username']) {
                    $r = "<img src=\"./../images/BlueRoad.png\" />";
                }
                else {
                    $r = "<img src=\"./../images/OrangeRoad.png\" />";
                }
            }
            
            return $r;
        }
	
	foreach ($board as $row) {
		foreach ($row as $tile) {
                    //$topleft = "";
                    //$topRight = "";
			switch ($tile->getRow()) {
				case 0:
					switch ($tile->getColumn()) {
						case 0:
							$tile00[0] = $tile->getResourceType();
							$tile00[1] = $tile->getRollNumber();
                            $tile00[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                            $tile00[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                            $tile00[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                            $tile00[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                            $tile00[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                            $tile00[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                            $tile00[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                            $tile00[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						case 1:
							$tile01[0] = $tile->getResourceType();
							$tile01[1] = $tile->getRollNumber();
                                                        $tile01[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile01[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile01[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile01[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile01[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile01[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile01[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile01[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");

							break;
						case 2:
							$tile02[0] = $tile->getResourceType();
							$tile02[1] = $tile->getRollNumber();
                                                        $tile02[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile02[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile02[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile02[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile02[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile02[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile02[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile02[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						case 3:
							$tile03[0] = $tile->getResourceType();
							$tile03[1] = $tile->getRollNumber();
                                                        $tile03[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile03[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile03[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile03[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile03[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile03[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile03[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile03[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						default:
							// Do nothing
							break;
					}
				case 1:
					switch ($tile->getColumn()) {
						case 0:
							$tile10[0] = $tile->getResourceType();
							$tile10[1] = $tile->getRollNumber();
                                                        $tile10[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile10[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile10[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile10[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile10[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile10[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile10[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile10[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						case 1:
							$tile11[0] = $tile->getResourceType();
							$tile11[1] = $tile->getRollNumber();
                                                        $tile11[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile11[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile11[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile11[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile11[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile11[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile11[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile11[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						case 2:
							$tile12[0] = $tile->getResourceType();
							$tile12[1] = $tile->getRollNumber();
                                                        $tile12[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile12[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile12[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile12[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile12[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile12[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile12[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile12[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						case 3:
							$tile13[0] = $tile->getResourceType();
							$tile13[1] = $tile->getRollNumber();
                                                        $tile13[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile13[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile13[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile13[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile13[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile13[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile13[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile13[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						default:
							// Do nothing
							break;
					}
				case 2:
					switch ($tile->getColumn()) {
						case 0:
							$tile20[0] = $tile->getResourceType();
							$tile20[1] = $tile->getRollNumber();
                                                        $tile20[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile20[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile20[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile20[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile20[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile20[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile20[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile20[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						case 1:
							$tile21[0] = $tile->getResourceType();
							$tile21[1] = $tile->getRollNumber();
                                                        $tile21[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile21[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile21[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile21[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile21[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile21[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile21[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile21[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						case 2:
							$tile22[0] = $tile->getResourceType();
							$tile22[1] = $tile->getRollNumber();
                                                        $tile22[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile22[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile22[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile22[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile22[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile22[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile22[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile22[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						case 3:
							$tile23[0] = $tile->getResourceType();
							$tile23[1] = $tile->getRollNumber();
                                                        $tile23[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile23[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile23[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile23[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile23[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile23[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile23[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile23[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						default:
							// Do nothing
							break;
					}
				case 3:
					switch ($tile->getColumn()) {
						case 0:
							$tile30[0] = $tile->getResourceType();
							$tile30[1] = $tile->getRollNumber();
                                                        $tile30[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile30[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile30[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile30[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile30[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile30[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile30[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile30[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						case 1:
							$tile31[0] = $tile->getResourceType();
							$tile31[1] = $tile->getRollNumber();
                                                        $tile31[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile31[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile31[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile31[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile31[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile31[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile31[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile31[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						case 2:
							$tile32[0] = $tile->getResourceType();
							$tile32[1] = $tile->getRollNumber();
                                                        $tile32[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile32[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile32[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile32[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile32[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile32[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile32[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile32[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						case 3:
							$tile33[0] = $tile->getResourceType();
							$tile33[1] = $tile->getRollNumber();
                                                        $tile33[2] = determineSettlementTypeAndColor($tile->getOccupation("topLeft"));
                                                        $tile33[3] = determineSettlementTypeAndColor($tile->getOccupation("topRight"));
                                                        $tile33[4] = determineSettlementTypeAndColor($tile->getOccupation("bottomLeft"));
                                                        $tile33[5] = determineSettlementTypeAndColor($tile->getOccupation("bottomRight"));
                                                        $tile33[6] = determineRoadTypeAndColor($tile->getRoad("top"), "h");
                                                        $tile33[7] = determineRoadTypeAndColor($tile->getRoad("right"), "v");
                                                        $tile33[8] = determineRoadTypeAndColor($tile->getRoad("bottom"), "h");
                                                        $tile33[9] = determineRoadTypeAndColor($tile->getRoad("left"), "v");
							break;
						default:
							// Do nothing
							break;
					}
			}
		}
	}
?>