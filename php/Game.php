<?php
    require_once 'boardLayout.php';
    require_once 'player.php';
    require_once 'InputValidator.php';
	require_once 'logManager.php';
    
    // Each game has a creator, two participants, and a board layout
    class Game {
		// Name of the game creator as entered when creating the game

		private static $POINTS_TO_WIN = 8;
		private static $INITIAL_STATE_TAG = "Initial";
		private static $GAME_SCHEMA_FILE = "./gameXML.xsd";
		private $creatorName;

		// Name of the game
		//private $gameName;
		// Leaving game name if we want to set a name
		private $gameID;

		// Names of participants who join the game
		/* @var $player1 Player */
		private $player1;
		/* @var $player2 Player */
		private $player2;

		// Board layout for the game
		/* @var $boardLayout BoardLayout */
		private $gameState;
		private $boardLayout;

		private $playersByID;

		// InputValidator object
		private $validator;
		private $gameLogFile;

		private $playerTurn;
		
		private $initialTurn;
		private $initialCount;
		
		public function __construct()
		{

		}

		public function getGameID()
		{
			return $this->gameID;
		}
        
		public function createGame($gameID, $creatorName, $player2, $gameXML) 
		{
			$this->validator = new InputValidator();
			if ($this->validator->ValidateUserName($creatorName) !== 1) 
			{
				//echo "invalid player name";
				throw new Exception("Invalid player name.");
			}

			if ($this->validator->ValidateUserName($player2) !== 1) 
			{
				//echo "invalid player name";
				throw new Exception("Invalid player name.");
			}

			$this->gameID = $gameID;
			$this->player1 = new Player($creatorName);
			$this->player2 = new Player($player2);
			$this->playersByID[$creatorName] = $this->player1;
			$this->playersByID[$player2] = $this->player2;
			$this->gameState = "Initial";
			$this->initialCount=0;
			$this->initialTurn="Settlement";
			$this->boardLayout = new BoardLayout();
			$this->boardLayout->createLayout();
			$this->playerTurn = $creatorName;

			$this->createGameXML($gameXML);
			$logManager = new LogManager();
			$this->gameLogFile = $logManager->createGameLogFile($gameID);
			$this->writeToLog("Game has started.\nPlayer 1: " . $creatorName . "\nPlayer 2: " . $player2);

		}
		
		// Line endings are automatically added to the passed string. 
		private function writeToLog($string)
		{
			if ($this->gameLogFile == "")
				throw new Exception("Log File not found. Unable to write to log.");
			
			$logHandle = fopen($this->gameLogFile, "a");
			fwrite($logHandle, $string . "\n");
			fclose($logHandle);
		}
		

		private function createGameXML($xmlFileName="")
		{
			if ($xmlFileName == "")
			{
				$gameManager = new GameManager();
				$xmlFileName = $gameManager->getGameXML($this->gameID);
			}
			
			$xmlDoc = new DOMDocument('1.0');
			//Pretty Print
			$xmlDoc->preserveWhiteSpace = false;
			$xmlDoc->formatOutput = true;
			
			$rootNode = $xmlDoc->createElement("CatanGame");
			$xmlDoc->appendChild($rootNode);
			
			$rootNode->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
			$rootNode->setAttribute("xsi:noNamespaceSchemaLocation", "file://" . self::$GAME_SCHEMA_FILE);
			
			
			$gameStateXML = $xmlDoc->createElement("GameState");
			if ($this->gameState == self::$INITIAL_STATE_TAG)
				$gameStateText = $xmlDoc->createTextNode($this->gameState . "-" . $this->initialCount . "-" . $this->initialTurn);
			else
				$gameStateText = $xmlDoc->createTextNode ($this->gameState);
			
			$gameStateXML->appendChild($gameStateText);
			$rootNode->appendChild($gameStateXML);
			
			$gameNumXML = $xmlDoc->createElement("GameNumber");
			$gameNumText = $xmlDoc->createTextNode($this->gameID);
			$gameNumXML->appendChild($gameNumText);
			$rootNode->appendChild($gameNumXML);
			
			$playersXML = $xmlDoc->createElement("Players");
			$playersXML->setAttribute("turn", $this->playerTurn);
			$rootNode->appendChild($playersXML);
			$playersXML->appendChild($this->player1->getPlayerXML($xmlDoc, "Player"));
			$playersXML->appendChild($this->player2->getPlayerXML($xmlDoc, "Player"));
			
			$rootNode->appendChild($this->boardLayout->getBoardLayoutXML($xmlDoc, "GameBoard"));
			
			$xmlDoc->save($xmlFileName);
			$this->validateGameXML($xmlFileName);
		}
		
		private function validateGameXML($xmlFileName)
		{
			$tempDOM = new DOMDocument();
			$tempDOM->load($xmlFileName);
			return $tempDOM->schemaValidate(self::$GAME_SCHEMA_FILE);
		}
		
		public function resumeGame($gameID)
		{
			$this->gameID = $gameID;
			$gameManager = new GameManager();
			
			$xmlFileName = $gameManager->getGameXML($this->gameID);
			
			if (!$this->validateGameXML($xmlFileName))
				throw new Exception("Bad Game XML");
			
			$gameXML = simplexml_load_file($xmlFileName);
			if ((string) $gameXML->GameNumber != $this->gameID)
				throw new Exception("Game ID doesn't match.");
			
			$playersXML = $gameXML->Players;
			$playerNodes = $playersXML->Player;
			if (count($playerNodes) != 2)
				throw new Exception("Bad Game XML.");
			
			$stateArr = explode("-", (string) $gameXML->GameState);
			$this->gameState = $stateArr[0];
			if (array_key_exists(1, $stateArr) && self::$INITIAL_STATE_TAG)
				$this->initialCount = intval ($stateArr[1]);
			if (array_key_exists(2, $stateArr) && self::$INITIAL_STATE_TAG)
				$this->initialTurn = $stateArr[2];
				
			
			$this->playerTurn = (string) $playersXML->attributes()->turn;
			$this->player1 = new Player((string) $playerNodes[0]->attributes()->id);
			$this->player1->reconstruct($playerNodes[0]);
			
			$this->player2 = new Player((string) $playerNodes[1]->attributes()->id);
			$this->player2->reconstruct($playerNodes[1]);
			
			// Consider removing Player1, Player2 variables and just using array
			$this->playersByID = array();

			$this->playersByID[(string) $this->player1->getPlayerID()] = $this->player1;
			$this->playersByID[(string) $this->player2->getPlayerID()] = $this->player2;
			
			$this->boardLayout = new BoardLayout();
			$this->boardLayout->reconstructLayout($gameXML->GameBoard);
			
			$logManager = new LogManager();
			$this->gameLogFile = $logManager->getOngoingLogFile($gameID);
			$this->checkWinningConditions();
		}
		
		public function performDieRoll()
		{	return rand(1, 6);	}
		
		
		public function setDice($die1, $die2)
		{
			if ($die1 > 6 || $die1 < 1 || $die2 > 6 || $die2 < 1)
				throw new Exception("Invalid Dice Numbers");
			
			$this->writeToLog("Dice have been rolled: " . $die1 . "," . $die2);
			
			//This section generates the resources for all the players based on the roll.
			
			$resTiles = $this->boardLayout->getTilesMatchingRoll($die1 + $die2);
			/* @var $resTile BoardTile */
			foreach($resTiles as $resTile)
			{
				// Returns an array/dict with player id being the key, and number of resources being the value.
				$resForPlayers = $resTile->getPlayersforResourceGeneration();
				
				foreach ($resForPlayers as $player => $resourceNum)
				{
					/* @var $playerToken Player */
					$playerToken = $this->getPlayerToken($player);
					$playerToken->addCard($resTile->getResourceType(), $resourceNum);
					$this->writeToLog("Resource Generation: Player " . $player . " gets " . $resourceNum . " " . $resTile->getResourceType() . " cards.");
				}
			}
			
			$this->createGameXML();
		}
		
		public function endInitialPlacement()
		{
			if ($this->gameState == "Initial")
			{
				$this->gameState = "Ongoing";
				$this->initialCount = 0;
				$this->initialTurn = "";
				$this->createGameXML();
				return true;						
			}
			return false;
		}
		
		public function getGameState()
		{	return $this->gameState;	}
		
		public function getInitialTurn()
		{	return $this->initialTurn;	}
		
		public function getPlayer($playerID)
		{
			if (array_key_exists($playerID, $this->playersByID))
				return $this->playersByID[$playerID];
			throw new Exception("Player not found.");
			
		}
		
		private function checkWinningConditions()
		{
			$winner = "";
			
			foreach ($this->playersByID as $player)
				if ($player->getVictoryPoints() >= self::$POINTS_TO_WIN)
				{
					$winner = $player->getPlayerID();
					break;
				}
			
			if (! $winner)
				return false;
			$this->gameState = "Completed";
			$logManager = new LogManager();
			$logManager->closeOngoingLog($this->gameID);
			$this->createGameXML();
			throw new GameOverException($winner); 
		}
		
		private function getPlayerToken($playerID)
		{
			if (!$this->isPlayer($playerID))
				throw new Exception("Invalid Player.");
			
			return $this->playersByID[$playerID];
		}
		
		public function getPlayersTurn()
		{	return $this->playerTurn;	}
		
		public function isPlayersTurn($playerID)
		{	return ($this->playerTurn == $playerID);	}
		
		public function endPlayersTurn()
		{	
			foreach(array_keys($this->playersByID) as $playerID)
				if ($this->playerTurn != $playerID)
				{
					$this->writeToLog("Player " . $this->playerTurn . " has ended his turn. Control now goes to " . $playerID);
					$this->playerTurn = $playerID;
					$this->createGameXML();
					$this->checkWinningConditions();
					return true;
				}
		}


		// Position can be left, right, top, bottom.
		// Need to check if possible to build before building (since it cost resources.
		public function buildRoad($playerID, $x, $y, $buildPosition)
		{
			if (!$this->isPlayersTurn($playerID))
				throw new Exception("Not Your Turn.");
			
			$playerToken = $this->getPlayer($playerID);
			if ( !$playerToken->canBuildRoad() )
				throw new Exception("Do not have the resources to build.");
			
			if (!$this->boardLayout->canBuildRoad($playerID, $x, $y, $buildPosition))
				throw new Exception("Can not build there.");
			
			$playerToken->buildRoad();
			$this->boardLayout->buildRoad($playerID, $x, $y, $buildPosition);
			
			$this->writeToLog("Player " . $playerID . " has built a road on tile (" . $x . "," . $y . "), position: " . $buildPosition);
			
			if ($this->gameState == self::$INITIAL_STATE_TAG)
			{
				$this->initialTurn = "Settlement";
				$this->endPlayersTurn();
				$this->initialCount++;
				if ($this->initialCount >= count($this->playersByID))
					$this->endInitialPlacement();
			}
			$this->createGameXML();
		}
		
		// Position can be topLeft, topRight, bottomLeft, or bottomRight.
		// Need to check if possible to build before building (since it cost resources.
		public function buildSettlement($playerID, $x, $y, $buildPosition)
		{
			if (!$this->isPlayersTurn($playerID))
				throw new Exception("Not Your Turn.");
			
			$playerToken = $this->getPlayer($playerID);
			if ( !$playerToken->canBuildSettlement() )
				throw new Exception("Do not have the resources to build.");
			
			$initialPlacement = false;
			if ($this->gameState == "Initial")
				$initialPlacement = true;

			if (!$this->boardLayout->canBuildSettlement($playerID, $x, $y, $buildPosition, $initialPlacement))
				throw new Exception("Can not build there.");
			
			$playerToken->buildSettlement();
			$this->boardLayout->buildSettlement($playerID, $x, $y, $buildPosition, $initialPlacement);
			$this->writeToLog("Player " . $playerID . " has built a settlement on tile (" . $x . "," . $y . "), position: " . $buildPosition);
			
			if ($initialPlacement)
				$this->initialTurn = "Road";
			
			$this->createGameXML();
			$this->checkWinningConditions();
		}
		
		// Position can be topLeft, topRight, bottomLeft, or bottomRight.
		// Need to check if possible to build before building (since it cost resources.
		public function buildCity($playerID, $x, $y, $buildPosition)
		{
			if (!$this->isPlayersTurn($playerID))
				throw new Exception("Not Your Turn.");
			
			$playerToken = $this->getPlayer($playerID);
			if ( !$playerToken->canBuildCity() )
				throw new Exception("Do not have the resources to build.");
			
			if (!$this->boardLayout->canBuildCity($playerID, $x, $y, $buildPosition))
				throw new Exception("Can not build there.");
			
			$playerToken->buildCity();
			$this->boardLayout->buildCity($playerID, $x, $y, $buildPosition);	
			
			$this->writeToLog("Player " . $playerID . " has built a city on tile (" . $x . "," . $y . "), position: " . $buildPosition);
			$this->createGameXML();
			$this->checkWinningConditions();
		}
		
		public function isPlayer($playerID)
		{
			return array_key_exists($playerID, $this->playersByID);
		}

    }
	
	class GameOverException extends Exception
	{
		private $winningPlayer = "";
		
		public function __construct($message, $code = 0, Exception $previous = null)
		{
			$this->winningPlayer = $message;
			parent::__construct($message, $code, $previous);
		}
		public function getErrorMessage()
		{	return "The game is now over. Player " . $this->getMessage() . " has won.";	}
		
		public function getWinningPlayerID()
		{	return $this->winningPlayer;	}
	}
 
?>