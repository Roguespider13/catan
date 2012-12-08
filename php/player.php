<?php

class Player {

	private static $CARDTYPES = array("brick", "lumber", "ore", "wheat", "wool");

	private $id;

	// Players start with these initial resources so they can build their initial settlements/roads.
	private $brickCards = 2;
	private $lumberCards = 2;
	private $oreCards = 0;
	private $wheatCards = 1;
	private $woolCards = 1;

	private $victoryPoints = 0;

	public function __construct($id) {
		$this->id = $id;
	}

	public function reconstruct($playerXML)
	{
		$this->victoryPoints = intval($playerXML->Points);
		$resNode = $playerXML->Resources;
		$this->brickCards = intval($resNode->Brick);
		$this->lumberCards = intval($resNode->Lumber);
		$this->oreCards = intval($resNode->Ore);
		$this->wheatCards = intval($resNode->Wheat);
		$this->woolCards = intval($resNode->Wool);
	}

	public function getPlayerID()
	{	return $this->id;	}

	public function getVictoryPoints()
	{	return $this->victoryPoints;	}

	public function addCard($cardType, $number) {
		if ($number > 4 && $number < 1)
			throw new Exception("Invalid number of cards");

		if (!in_array($cardType, self::$CARDTYPES))
			throw new Exception("Invalid Card Type");

		$cardType = strtolower($cardType);
		switch ($cardType) {
			case "brick":
				$this->brickCards += $number;
				break;
			case "lumber":
				$this->lumberCards += $number;
				break;
			case "ore":
				$this->oreCards += $number;
				break;
			case "wheat":
				$this->wheatCards += $number;
				break;
			case "wool":
				$this->woolCards += $number;
				break;
		}
	}

	public function getCardArray()	{
		$cardArray = array(
			"brick" => $this->brickCards,
			"lumber" => $this->lumberCards,
			"ore" => $this->oreCards,
			"wheat" => $this->wheatCards,
			"wool" => $this->woolCards
			);

		return $cardArray;
	}

	public function canBuildRoad()
	{
		if ($this->brickCards >= 1 && $this->lumberCards >= 1)
			return true;
		return false;
	}

	public function buildRoad()	{
		if ($this->brickCards < 1 || $this->lumberCards < 1)
			//return false;
			throw new Exception("Unavailable Resources");


		$this->brickCards  -= 1;
		$this->lumberCards -= 1;
		return true;
	}

	public function canBuildSettlement()
	{
		if ($this->brickCards >= 1 && $this->lumberCards >= 1 && $this->wheatCards >= 1 && $this->woolCards >= 1)
			return true;

		return false;
	}


	/*
	 * Settlements are built with 1 of each card, except ore.
	 * Settlements provide 1 Victory Point.
	 * Settlements must be placed at least 2 edges away from the nearest city/settlement -
	 *	this will be handled by board mechanics rather than player.
	 */
	public function buildSettlement()	{
		if ($this->brickCards < 1 || $this->lumberCards < 1 || $this->wheatCards < 1 || $this->woolCards < 1)
			//return false;
			throw new Exception("Unavailable Resources");

		$this->brickCards  -= 1;
		$this->lumberCards -= 1;
		$this->wheatCards  -= 1;
		$this->woolCards   -= 1;

		$this->victoryPoints += 1;
		return true;

	}

	public function canBuildCity()
	{
		if ($this->wheatCards >= 2 && $this->oreCards >= 3)
			return true;
		return false;
	}

	/*
	 * Cities are built with 2 wheat cards and 3 ore cards.
	 * Cities provide 2 victory points but are upgraded from Settlements,
	 *	so player only gains 1 additional victory point.
	 */
	public function buildCity()	{
		if ($this->wheatCards < 2 || $this->oreCards < 3)
			//return false;
			throw new Exception("Unavailable Resources");

		$this->wheatCards -= 2;
		$this->oreCards -= 3;

		$this->victoryPoints += 1;

		return true;
	}
	
	public function declareForfeitWinner($pointsToWin)
	{	$this->victoryPoints=$pointsToWin;	}

	public function getPlayerXML($xmlDoc, $xmlTag)
	{
		/* @var $xmlDoc DOMDocument */
		$playerXML = $xmlDoc->createElement($xmlTag);
		$playerXML->setAttribute("id", $this->id);
		$pointsXML = $xmlDoc->createElement("Points");
		$playerXML->appendChild($pointsXML);
		$pointsText = $xmlDoc->createTextNode($this->victoryPoints);
		$pointsXML->appendChild($pointsText);

		//Resources
		$resXML = $xmlDoc->createElement("Resources");
		$playerXML->appendChild($resXML);

		$brickXML = $xmlDoc->createElement("Brick");
		$brickText = $xmlDoc->createTextNode($this->brickCards);
		$brickXML->appendChild($brickText);
		$resXML->appendChild($brickXML);

		$lumberXML = $xmlDoc->createElement("Lumber");
		$lumberText = $xmlDoc->createTextNode($this->lumberCards);
		$lumberXML->appendChild($lumberText);
		$resXML->appendChild($lumberXML);

		$oreXML = $xmlDoc->createElement("Ore");
		$oreText = $xmlDoc->createTextNode($this->oreCards);
		$oreXML->appendChild($oreText);
		$resXML->appendChild($oreXML);

		$wheatXML = $xmlDoc->createElement("Wheat");
		$wheatText = $xmlDoc->createTextNode($this->wheatCards);
		$wheatXML->appendChild($wheatText);
		$resXML->appendChild($wheatXML);

		$woolXML = $xmlDoc->createElement("Wool");
		$woolText = $xmlDoc->createTextNode($this->woolCards);
		$woolXML->appendChild($woolText);
		$resXML->appendChild($woolXML);

		return $playerXML;
	}
}

?>
