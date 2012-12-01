<?php

class BoardTile	{
	
	private static $RESOURCE_TYPES = array("brick", "desert", "lumber", "ore", "wheat", "wool");
	//private static $SETTLECITY_POS = array("topLeft", "topRight", "bottomLeft" , "bottomRight");
	//private static $ROAD_POS = array("top", "left", "right", "bottom");
	
	private $row = 0;
	private $col=0;
	
	private $topRoad = "";
	private $bottomRoad = "";
	private $rightRoad = "";
	private $leftRoad = "";
	
	private $topLeftCorner = "";
	private $topRightCorner = "";
	private $bottomLeftCorner = "";
	private $bottomRightCorner = "";
	
	private $resourceType = "desert";
	private $rollNumber = 0;
	
	public function __construct($row, $column) {
		$this->row = $row;
		$this->col = $column;
	}
	
	public function createXML($xmlDoc, $tileTag)
	{
		$tileXML = $xmlDoc->createElement($tileTag);
		$tileXML->setAttribute("Row", $this->row);
		$tileXML->setAttribute("Col", $this->col);
		$tileXML->setAttribute("ResType", $this->getResourceType());
		$tileXML->setAttribute("DieNumber", $this->rollNumber);
		//Roads
		$tileXML->setAttribute("TR", $this->topRoad);
		$tileXML->setAttribute("LR", $this->leftRoad);
		$tileXML->setAttribute("RR", $this->rightRoad);
		$tileXML->setAttribute("BR", $this->bottomRoad);
		//Occupations (City/Settlement)
		$tileXML->setAttribute("TLC", $this->topLeftCorner);
		$tileXML->setAttribute("TRC", $this->topRightCorner);
		$tileXML->setAttribute("BLC", $this->bottomLeftCorner);
		$tileXML->setAttribute("BRC", $this->bottomRightCorner);
		
		return $tileXML;
	}
	
	public function reconstructTile($tileXML)
	{
		$this->row = intval($tileXML->attributes()->Row);
		$this->col = intval($tileXML->attributes()->Col);
		$this->setResourceType((string) $tileXML->attributes()->ResType);
		$this->setRollNumber(intval($tileXML->attributes()->DieNumber));
		
		$this->topRoad = (string) $tileXML->attributes()->TR;
		$this->rightRoad = (string) $tileXML->attributes()->RR;
		$this->leftRoad = (string) $tileXML->attributes()->LR;
		$this->bottomRoad = (string) $tileXML->attributes()->BR;
		
		$this->topLeftCorner = (string) $tileXML->attributes()->TLC;
		$this->topRightCorner = (string) $tileXML->attributes()->TRC;
		$this->bottomLeftCorner = (string) $tileXML->attributes()->BLC;
		$this->bottomRightCorner = (string) $tileXML->attributes()->BRC;
	}
	
	public function getRow()
	{	return $this->row;	}
	
	public function getColumn()
	{	return $this->col;	}
	
	
	public function setResourceType($type)
	{
		if (! in_array($type, self::$RESOURCE_TYPES))
				throw new Exception("Invalid Resource Type");
		
		$this->resourceType = $type;
	}
	
	public function setRollNumber($number)	{
		if ($number < 2 || $number > 12)
			throw new Exception("Invalid Chit Number");
		
		$this->rollNumber = $number;
	}
	
	public function buildRoad($playerID, $position)
	{
		// Not checking position type as getRoad already does this.
		if ($this->getRoad($position) != "")
			throw new Exception("Road already Exists.");
		
		switch ($position)
		{
			case "top":
				$this->topRoad = $playerID;
				break;
			case "left":
				$this->leftRoad = $playerID;
				break;
			case "right":
				$this->rightRoad = $playerID;
				break;
			case "bottom":
				$this->bottomRoad = $playerID;
				break;			
		}
	}
	
	public function buildSettlement($playerID, $position)
	{
		if ($this->getOccupation($position) != "")
			throw new Exception("Position already occupied");
		
		
		$this->occupy($playerID, $position, "S");
	}
	
	public function buildCity($playerID, $position)
	{
		// A settlement (belonging to the player) must already exist at this location
		if ($this->getOccupation($position) != "S." . $playerID)
				throw new Exception("Invalid City Location");
		
		$this->occupy($playerID, $position, "C");
	}
	
	private function occupy($playerID, $position, $occupyType)
	{
		$SETTLECITY_POS = array("topLeft", "topRight", "bottomLeft" , "bottomRight");
		
		if (!in_array($position, $SETTLECITY_POS))
				throw new Exception("Invalid Build Position");
		
		switch($position)
		{
			case "topLeft":
				$this->topLeftCorner = $occupyType . "." . $playerID;
				break;			
			case "topRight":
				$this->topRightCorner = $occupyType . "." . $playerID;
				break;			
			case "bottomLeft":
				$this->bottomLeftCorner = $occupyType . "." . $playerID;
				break;			
			case "bottomRight":
				$this->bottomRightCorner = $occupyType . "." . $playerID;
				break;			
		}
	}
	
	public function getOccupation($position)
	{
		$SETTLECITY_POS = array("topLeft", "topRight", "bottomLeft" , "bottomRight");
		
		if (!in_array($position, $SETTLECITY_POS))
				throw new Exception("Invalid Build Position");
		
		switch($position)
		{
			case "topLeft":
				return $this->topLeftCorner;
				break;			
			case "topRight":
				return $this->topRightCorner;
				break;			
			case "bottomLeft":
				return $this->bottomLeftCorner;
				break;			
			case "bottomRight":
				return $this->bottomRightCorner;
				break;	
		}
	}
	
	public function getPlayerOccupation($position)
	{
		$occupationString = $this->getOccupation($position);
		if ($occupationString == "")
			return "";
		
		$expOcc = explode(".", $occupationString);
		return $expOcc[1];
	}
	
	public function getPlayersforResourceGeneration()
	{
		$resArray = array();
		$resArray = $this->updateResources($this->topLeftCorner, $resArray);
		$resArray = $this->updateResources($this->topRightCorner, $resArray);
		$resArray = $this->updateResources($this->bottomLeftCorner, $resArray);
		$resArray = $this->updateResources($this->bottomRightCorner, $resArray);
		return $resArray;
	}
	
	private function updateResources($occupation, $resArray)
	{
		
		if ($occupation != "")
		{	
			$occ = explode(".", $occupation);
			
			# 1 card for settlement (default), 2 for City
			$resNum = 1;
			if ($occ[0] == "C")
				$resNum = 2;
			
			if (array_key_exists($occ[1], $resArray))
				$resArray[$occ[1]] = $resArray[$occ[1]] + $resNum;
			else
				$resArray[$occ + 1] = $resNum;
		}
		
		return $resArray;
	}
	
	public function getRoad($position)
	{
		$ROAD_POS = array("top", "left", "right", "bottom");
		
		if (!in_array($position, $ROAD_POS))
				throw new Exception("Invalid Road Position");
		
		switch ($position)
		{
			case "top":
				return $this->topRoad;
				break;
			case "left":
				return $this->leftRoad;
				break;
			case "right":
				return $this->rightRoad;
				break;
			case "bottom":
				return $this->bottomRoad;
				break;			
		}
		
	}
	
	public function getResourceType()
	{	return $this->resourceType;	}
	
	public function getRollNumber()
	{	return $this->rollNumber;	}
	
}

class BoardLayout	{
	
	private $xmlFile = "";
	private $boardLayout;
	private $robber;
	private $tilesByDieNumber = array();
	
	public function __construct() {
		
	}
	
	public function createLayout() {
		$dieArray = array(2,3,4,5,6,8,9,10,11,12);
		$extraDie = array(3,4,5,6,8,9,10,11);
		$resourceArray = array("desert", "brick", "brick", "brick", "lumber", "lumber", "lumber", "ore", "ore", "ore", "wheat", "wheat", "wheat", "wool", "wool", "wool");
		
		for ($i=0; $i < 5; $i++)
		{
			$index = rand(0, count($extraDie)-1);
			$dieArray[] = $extraDie[$index];
			unset($extraDie[$index]);
			$extraDie = array_values($extraDie);
		}
		
		for ($i=0; $i < 3; $i++)
		{
			shuffle($dieArray);
			shuffle($resourceArray);
		}
		
		$this->generateGameBoard($resourceArray, $dieArray);
	}
	
	public function reconstructLayout($boardXML)
	{
		$this->fillDieArray();
		$this->robber["Row"] = intval($boardXML->Robber->attributes()->Row);
		$this->robber["Col"] = intval($boardXML->Robber->attributes()->Col);
		
		$tiles = $boardXML->Tiles->Tile;
		
		$this->boardLayout = array_fill(0, 4, array_fill(0, 4, 0));
		
		foreach ($tiles as $tileXML)
		{
			$tempTile = new BoardTile(0,0);
			$tempTile->reconstructTile($tileXML);
			$this->boardLayout[$tempTile->getRow()] [$tempTile->getColumn()] = $tempTile;
			$this->tilesByDieNumber[$tempTile->getRollNumber()][] = $tempTile;
		}
	}
	
	public function getTileByPosition($row, $column)
	{
		if ($row > 3 || $column > 3)
			throw new Exception("Invalid board position.");
		
		return $this->boardLayout[$row][$column];
	}
	
	public function getTilesMatchingRoll($rollNumber)
	{
		if ($rollNumber > 12 || $rollNumber < 2)
			throw new Exception("Invalid Roll Number.");		
			
		return $this->tilesByDieNumber[$rollNumber];
	}
	
	/*
	 * @param $xmlDoc DOMDocument
	 * @param $topTag String
	 */
	public function getBoardLayoutXML($xmlDoc, $topTag)
	{
		/* @var $xmlDoc DOMDocument */
		$boardXML = $xmlDoc->createElement($topTag);
		$robberXML = $xmlDoc->createElement("Robber");
		$boardXML->appendChild($robberXML);
		$robberXML->setAttribute("Row", $this->robber["Row"]);
		$robberXML->setAttribute("Col", $this->robber["Col"]);
		
		$topTilesXML = $xmlDoc->createElement("Tiles");
		$boardXML->appendChild($topTilesXML);
		
		foreach ($this->boardLayout as $row)
		{
			foreach ($row as $boardTile)
			{
				/* @var $boardTile BoardTile			 */
				
				$topTilesXML->appendChild($boardTile->createXML($xmlDoc, "Tile"));
				
			}
		}
		return $boardXML;
	}
	
	public function getGameBoard()
	{
		return $this->boardLayout;	
	}
	
	private function fillDieArray()
	{
		$this->tilesByDieNumber = array_fill(2, 11, array());
	}
	
	private function generateGameBoard($resourceLayout, $dieLayout)	{
		// Board is 4x4 with 16 resource tiles. Only 15 die rolls because the single desert tile must be 7. 
		if (count($resourceLayout) < 16 || count($dieLayout) < 15)
			throw new Exception("Incorrect resource/die layout.");
		
		$this->boardLayout = array();
		$this->fillDieArray();
		$resourceIndex = 0;
		$dieIndex = 0;
		
		for ($row=0; $row < 4; $row++)
		{
			for($column=0; $column < 4; $column++)
			{
				$tile = new BoardTile($row, $column);
				
				$rollNumber = 7;
				$resType = $resourceLayout[$resourceIndex];
				$resourceIndex++;
				if ($resType != "desert")
				{
					$rollNumber = $dieLayout[$dieIndex];
					$dieIndex++;
				}
				else
				{
					$this->robber = array("Row" => $row, "Col" => $column);
				}

				$tile->setResourceType($resType);
				$tile->setRollNumber($rollNumber);
				$this->boardLayout[$row][$column] = $tile;
				
				$this->tilesByDieNumber[$rollNumber][] = $tile;
			}
		}
	}
	
	
	/* Unlike the complexity of building a road or settlement.
	 * A city can only be built where a settlement exists. It doesn't
	 * depend on linking structures.
	 */
	public function canBuildCity($playerID, $x, $y, $position)
	{
		if (!$this->insideBoardBoundaries($x, $y))
			throw new Exception("Outside board boundaries.");
		
		$tile = $this->boardLayout[$x][$y];
		if ($tile->getPlayerOccupation($position) != $playerID)
			return false;
		return true;
	}
	
	public function buildCity($playerID, $x, $y, $position)
	{
		if (! $this->canBuildCity($playerID, $x, $y, $position))
			throw new Exception("Cannot build there.");
		
		$tile = $this->boardLayout[$x][$y];
		$tile->buildCity($playerID, $position);
		
		$this->updateBuilding($playerID, $x, $y, $position, "city");
	}
	
	
	// Used to update tiles around current one when buildin Cities and Settlements.
	private function updateBuilding($playerID, $x, $y, $position, $type)
	{
		switch ($position)
		{
			case "topRight":
				if ($x > 0)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x-1][$y], "bottomRight", $type);
					//$this->boardLayout[$x-1][$y]->buildCity($playerID, "bottomRight");
				if ($y < 3)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x][$y+1], "topLeft", $type);
					//$this->boardLayout[$x][$y+1]->buildCity($playerID, "topLeft");
				if ($x> 0 && $y < 3)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x-1][$y+1], "bottomLeft", $type);
					//$this->boardLayout[$x-1][$y+1]->buildCity($playerID, "bottomLeft");
				break;
				
			case "topLeft":
				if ($x > 0)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x-1][$y], "bottomLeft", $type);
					//$this->boardLayout[$x-1][$y]->buildCity($playerID, "bottomLeft");
				if ($y > 0)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x][$y-1], "topRight", $type);
					//$this->boardLayout[$x][$y-1]->buildCity($playerID, "topRight");
				if ($x> 0 && $y > 0)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x-1][$y-1], "bottomRight", $type);
					//$this->boardLayout[$x-1][$y-1]->buildCity($playerID, "bottomRight");
				break;
				
			case "bottomRight":
				if ($x < 3)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x+1][$y], "topRight", $type);
					//$this->boardLayout[$x+1][$y]->buildCity($playerID, "topRight");
				if ($y < 3)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x][$y+1], "bottomLeft", $type);
					///$this->boardLayout[$x][$y+1]->buildCity($playerID, "bottomLeft");
				if ($x < 3 && $y < 3)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x+1][$y+1], "topLeft", $type);
					//$this->boardLayout[$x+1][$y+1]->buildCity($playerID, "topLeft");
				break;
			case "bottomLeft":
				if ($x < 3)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x+1][$y], "topLeft", $type);
					//$this->boardLayout[$x+1][$y]->buildCity($playerID, "topLeft");
				if ($y > 0)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x][$y-1], "bottomRight", $type);
					//$this->boardLayout[$x][$y-1]->buildCity($playerID, "bottomRight");
				if ($x < 3 && $y > 0)
					$this->updateBuildingTile($playerID, $this->boardLayout[$x+1][$y+1], "topRight", $type);
					//$this->boardLayout[$x+1][$y+1]->buildCity($playerID, "topRight");
				break;
		}
	}
	
	// Used to update the specific tilesa and call the correct function based on type (Settlement/City).
	private function updateBuildingTile($playerID, $tile, $position, $type)
	{
		if ($type == "city")
			$tile->buildCity($playerID, $position);
		if ($type == "settlement")
			$tile->buildSettlement($playerID, $position);		
	}
	
	
	/*
	 * Building a settlement requires 2 things
	 * 1. A connecting road
	 * 2. No other settlements 1 road length away (even if road doesn't exist).
	 * This requires a lot of checking
	 */
	public function canBuildSettlement($playerID, $x, $y, $position, $initialPlacement)
	{
		if (!$this->insideBoardBoundaries($x, $y))
			throw new Exception("Outside board boundaries.");
		
		$tile = $this->boardLayout[$x][$y];
		$settlePoint = $tile->getOccupation($position);
		$adjTile1 = "";
		$adjTile2 = "";
		$adjRoads = array();
		$adjOccupations = array();
		
		if (preg_match('/^top/', $position))	{	
			$adjRoads[] = $tile->getRoad("top");
			if ($x > 0)
				$adjTile1 = $this->boardLayout[$x-1][$y];
		}
		if (preg_match('/^bottom/', $position))	{
			$adjRoads[] = $tile->getRoad("bottom");	
			if ($x < 3)
				$adjTile1 = $this->boardLayout[$x+1][$y];
		}
		if (preg_match('/Right$/', $position))	{	
			$adjRoads[] = $tile->getRoad("right");
			if ($y < 3)
				$adjTile2 = $this->boardLayout[$x][$y+1];
		}
		if (preg_match('/Left$/', $position))	{	
			$adjRoads[] = $tile->getRoad("left");
			if ($y > 0)
				$adjTile2 = $this->boardLayout[$x][$y-1];
		}

		if ($adjTile1 && $adjTile1->getOccupation($position))
			$adjOccupations[] = $adjTile1->getOccupation($position);
		if ($adjTile2 && $adjTile1->getOccupation($position))
			$adjOccupations[] = $adjTile2->getOccupation($position);
		
		switch ($position)
		{
			case "topRight":
				if ($tile->getOccupation("topLeft"))
					$adjOccupations[] = $tile->getOccupation("topLeft");
				if ($tile->getOccupation("bottomRight"))
					$adjOccupations[] = $tile->getOccupation("bottomRight");
				
				if ($adjTile1)
					$adjRoads[] = $adjTile1->getRoad("right");

				if ($adjTile2)
					$adjRoads[] = $adjTile2->getRoad("top");
				break;
			
			case "topLeft":
				if ($tile->getOccupation("topRight"))
					$adjOccupations[] = $tile->getOccupation("topRight");
				if ($tile->getOccupation("bottomLeft"))
					$adjOccupations[] = $tile->getOccupation("bottomLeft");
						
				if ($adjTile1)
					$adjRoads[] = $adjTile1->getRoad("left");
				if ($adjTile2)
					$adjRoads[] = $adjTile2->getRoad("top");
				break;
			case "bottomLeft":
				if ($tile->getOccupation("topLeft"))
				$adjOccupations[] = $tile->getOccupation("topLeft");
				if ($tile->getOccupation("bottomRight"))
				$adjOccupations[] = $tile->getOccupation("bottomRight");
						
				if ($adjTile1)
					$adjRoads[] = $adjTile1->getRoad("left");
				if ($adjTile2)
					$adjRoads[] = $adjTile2->getRoad("bottom");
				break;
			case "bottomRight":
				if ($tile->getOccupation("topRight"))
					$adjOccupations[] = $tile->getOccupation("topRight");
				if ($tile->getOccupation("bottomLeft"))
					$adjOccupations[] = $tile->getOccupation("bottomLeft");
						
				if ($adjTile1)
					$adjRoads[] = $adjTile1->getRoad("right");
				if ($adjTile2)
					$adjRoads[] = $adjTile2->getRoad("bottom");
				break;
			
		}
		
		if ($settlePoint != "")
			return false;
		
		// Return false if there are now adjacent roads, though this isn't a requirement if its the initial placement of settlements
		if ((! in_array($playerID, $adjRoads)) && ! $initialPlacement)
			return false;
		
		if (! empty($adjOccupations))
			return false;

		
		return true;
	}
	
	public function buildSettlement($playerID, $x, $y, $position, $initialPlacement=false)
	{
		if (! $this->canBuildSettlement($playerID, $x, $y, $position, $initialPlacement))
			throw new Exception("Cannot build there.");
		
		$tile = $this->boardLayout[$x][$y];
		$tile->buildSettlement($playerID, $position);
		
		$this->updateBuilding($playerID, $x, $y, $position, "settlement");
	}
	
	/*
	 * Building a road requires 1 of 2 things:
	 * 1. A road to connect to
	 * 2. A settlement to connect to.
	 * Even more checking.
	 */
	public function canBuildRoad($playerID, $x, $y, $position)
	{
		if (!$this->insideBoardBoundaries($x, $y))
			throw new Exception("Outside board boundaries.");
		
		$tile = $this->boardLayout[$x][$y];
		
		if ($tile->getRoad($position) != "")
			return false;
		
		if ($x > 0)
			$upperTile = $this->boardLayout[$x-1][$y];
		if ($x < 3)
			$lowerTile = $this->boardLayout[$x+1][$y];
		if ($y > 0)
			$leftTile = $this->boardLayout[$x][$y-1];
		if ($y < 3)
			$rightTile = $this->boardLayout[$x][$y+1];
		
		// In this case adjRoads isn't just roads but also connecting settlements/cities.
		$adjRoads = array();
		
		
		switch ($position)
		{
			case "top":
				if ($tile->getRoad("top") != "")
					return false;
				$adjRoads[] = $tile->getRoad("left");
				$adjRoads[] = $tile->getRoad("right");
				if ($leftTile) //Left
					$adjRoads[] = $leftTile->getRoad("top");
				if ($upperTile) //Upper
				{
					$adjRoads[] = $upperTile->getRoad("left");
					$adjRoads[] = $upperTile->getRoad("right");
				}
				if ($rightTile) //Right
					$adjRoads[] = $rightTile->getRoad("top");
				
				$adjRoads[] = $tile->getPlayerOccupation("topLeft");
				$adjRoads[] = $tile->getPlayerOccupation("topRight");
				break;
				
			case "left":
				if ($tile->getRoad("left") != "")
					return false;
				$adjRoads[] = $tile->getRoad("top");
				$adjRoads[] = $tile->getRoad("bottom");
				if ($lowerTile) //Bottom
					$adjRoads[] = $lowerTile->getRoad("left");
				if ($leftTile) //Left
				{
					$adjRoads[] = $leftTile->getRoad("top");
					$adjRoads[] = $leftTile->getRoad("bottom");
				}
				if ($upperTile) // Upper
					$adjRoads[] = $upperTile->getRoad("left");
				$adjRoads[] = $tile->getPlayerOccupation("topLeft");
				$adjRoads[] = $tile->getPlayerOccupation("topRight");
				break;
			case "right":
				if ($tile->getRoad("right") != "")
					return false;
				$adjRoads[] = $tile->getRoad("top");
				$adjRoads[] = $tile->getRoad("bottom");
				if ($upperTile) //Upper
					$adjRoads[] = $upperTile->getRoad("right");
				if ($rightTile) //Right
				{
					$adjRoads[] = $rightTile->getRoad("top");
					$adjRoads[] = $rightTile->getRoad("bottom");
				}
				if ($lowerTile) // Lower
					$adjRoads[] = $lowerTile->getRoad("right");
				$adjRoads[] = $tile->getPlayerOccupation("topRight");
				$adjRoads[] = $tile->getPlayerOccupation("bottomRight");
				break;
			case "bottom":
				if ($tile->getRoad("bottom") != "")
					return false;
				$adjRoads[] = $tile->getRoad("left");
				$adjRoads[] = $tile->getRoad("right");
				if ($rightTile) //Right
					$adjRoads[] = $rightTile->getRoad("bottom");
				if ($lowerTile) //Lower
				{
					$adjRoads[] = $lowerTile->getRoad("left");
					$adjRoads[] = $lowerTile->getRoad("right");
				}
				if ($leftTile) //Left
					$adjRoads[] = $leftTile->getRoad("bottom");
				
				$adjRoads[] = $tile->getPlayerOccupation("bottomLeft");
				$adjRoads[] = $tile->getPlayerOccupation("bottomRight");
				break;
		}

		if (in_array($playerID, $adjRoads))
			return true;
		
		return false;	
	}
	
	public function buildRoad($playerID, $x, $y, $position)
	{
		if (! $this->canBuildRoad($playerID, $x, $y, $position))
			throw new Exception("Cannot Build there.");
		
		$tile = $this->boardLayout[$x][$y];
		$tile->buildRoad($playerID, $position);
		$this->updateRoad($playerID, $x, $y, $position);
	}
	
	public function updateRoad($playerID, $x, $y, $position)
	{
		switch($position)
		{
			case "top":
				if ($x > 0)
					$this->boardLayout[$x-1][$y]->buildRoad($playerID, "bottom");
				break;
			case "bottom":
				if ($x < 3)
					$this->boardLayout[$x+1][$y]->buildRoad($playerID, "top");
				break;
			case "left":
				if ($y > 0)
					$this->boardLayout[$x][$y-1]->buildRoad($playerID, "right");
				break;
			case "right":
				if ($x < 3)
					$this->boardLayout[$x][$y+1]->buildRoad($playerID, "left");
				break;
		}
	}
		
	private function insideBoardBoundaries($x, $y)
	{
		if ($x < 0 || $y < 0 || $x > 3 || $y > 3)
			return false;
		return true;
	}
}
?>
