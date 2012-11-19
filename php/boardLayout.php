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
		$this->row = $tileXML->attributes()->Row;
		$this->col = $tileXML->attributes()->Col;
		$this->setResourceType($tileXML->attributes()->ResType);
		$this->setRollNumber($tileXML->attributes()->DieNumber);
		
		$this->topRoad = $tileXML->attributes()->TR;
		$this->rightRoad = $tileXML->attributes()->RR;
		$this->leftRoad = $tileXML->attributes()->LR;
		$this->bottomRoad = $tileXML->attributes()->BR;
		
		$this->topLeftCorner = $tileXML->attributes()->TLC;
		$this->topRightCorner = $tileXML->attributes()->TRC;
		$this->bottomLeftCorner = $tileXML->attributes()->BLC;
		$this->bottomRightCorner = $tileXML->attributes()->BRC;
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
	
	public function createLayout() {
		$dieArray = array(2,3,4,5,6,8,9,10,11,12);
		$extraDie = array(3,4,5,6,8,9,10,11);
		$resourceArray = array("desert", "brick", "brick", "brick", "lumber", "lumber", "lumber", "ore", "ore", "ore", "wheat", "wheat", "wheat", "wool", "wool", "wool");
		
		for ($i=0; $i < 5; $i++)
		{
			$index = rand(0, count($extraDie)-1);
			$dieArray[] = $extraDie[$index];
			unset($extraDie[$index]);			
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
		$this->robber["Row"] = $boardXML->Robber->attributes()->Row;
		$this->robber["Col"] = $boardXML->Robber->attributes()->Col;
		
		$tiles = $boardXML->Tiles->Tile;
		
		$this->boardLayout = array_fill(0, 4, array_fill(0, 4, 0));
		
		foreach ($tiles as $tileXML)
		{
			$tempTile = BoardTile(0,0);
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
				$this->boardArray[$row][$column] = $tile;
				
				$this->tilesByDieNumber[$rollNumber][] = $tile;
			}
		}
	}
}
?>
