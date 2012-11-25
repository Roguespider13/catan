<?php
    require_once 'boardLayout.php';
    require_once 'player.php';
    require_once 'InputValidator.php';
	require_once 'logManager.php';
    
    // Each game has a creator, two participants, and a board layout
    class Game {
        // Name of the game creator as entered when creating the game
		
		private static $POINTS_TO_WIN = 8;
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
		
		public function __construct()
		{
			
		}
		
		public function getGameID()
		{
			return $this->gameID;
		}
        
        public function createGame($gameID, $creatorName, $player2) {
            $this->validator = new InputValidator();
            if ($this->validator->ValidateUserName($creatorName) !== 1) {
                //echo "invalid player name";
                throw new Exception("Invalid player name.");
            }
			
			if ($this->validator->ValidateUserName($player2) !== 1) {
                //echo "invalid player name";
                throw new Exception("Invalid player name.");
            }
            
/*            if ($this->validator->ValidateName($gameName) !== 1) {
                //echo "invalid game name";
                throw new Exception("Invalid game name.");
            }
 */          
            //$this->creatorName = new Player($creatorName);
			$this->gameID = $gameID;
            $this->player1 = new Player($creatorName);
            $this->player2 = new Player($player2);
			$this->playersByID[$creatorName] = $this->player1;
			$this->playersByID[$player2] = $this->player2;
			$this->gameState = "Initial";
            $this->boardLayout = new BoardLayout();
			$this->boardLayout->createLayout();
			
			$this->createGameXML();
			$logManager = new LogManager();
			$this->gameLogFile = $logManager->createGameLogFile($gameID);
			$this->writeToLog("Game has started.\Player 1: " . $creatorName . "\nPlayer 2: " . $player2);
			$this->playerTurn = $creatorName;
        }
		
		// Line endings are automatically added to the passed string. 
		private function writeToLog($string)
		{
			if ($this->gameLogFile == "")
				throw new Exception("Log File not found. Unable to write to log.");
			
			$logHandle = fopen($this->gameID, "a");
			fwrite($logHandle, $string . "\n");
			fclose($logHandle);
		}
		

		private function createGameXML()
		{
			$gameManager = new GameManager();
			$xmlFileName = $gameManager->getGameXML($this->gameID);
			
			$xmlDoc = new DOMDocument('1.0');
			$rootNode = $xmlDoc->createElement("CatanGame");
			$xmlDoc->appendChild($rootNode);
			
			$gameStateXML = $xmlDoc->createElement("GameState");
			$gameStateText = $xmlDoc->createTextNode($this->gameState);
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
		}
		
		public function resumeGame($gameID)
		{
			$xmlFileName = GameManager::getGameXML($this->gameID);
			$gameXML = simplexml_load_file($xmlFileName);
			if ($gameXML->GameNumber != $this->gameID)
				throw new Exception("Bad Game XML.");
			$playersXML = $gameXML->Players;
			
			if (count($playersXML) != 2)
				throw new Exception("Bad Game XML.");
			
			$this->gameState = $gameXML->GameState;
			$this->playerTurn = $playersXML->attributes()->turn;
			$this->player1 = Player($playersXML[0]->attributes()->id);
			$this->player1->reconstruct($playersXML[0]);
			
			$this->player2 = Player($playersXML[1]->attributes()->id);
			$this->player2->reconstruct($playersXML[1]);
			
			$this->boardLayout = BoardLayout::reconstructLayout($gameID->GameBoard);
			
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
				$this->gameState == "Ongoing";
				return true;						
			}
			return false;
		}
		
		private function checkWinningConditions()
		{
			$gameOver = false;
			$winner = "";
			if ($this->player1->getVictoryPoints() == self::$POINTS_TO_WIN)
				$winner = $this->player1->getPlayerID();
			if ($this->player2->getVictoryPoints() == self::$POINTS_TO_WIN)
				$winner = $this->player2->getPlayerID();
			
			if (! $gameOver)
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
			$playerToken = $this->getPlayer($playerID);
			if ( !$playerToken->canBuildRoad() )
				throw new Exception("Do not have the resources to build.");
			
			if (!$this->boardLayout->canBuildRoad($playerID, $x, $y, $buildPosition))
				throw new Exception("Can not build there.");
			
			$playerToken->buildRoad();
			$this->boardLayout->buildRoad($playerID, $x, $y, $buildPosition);
			
			$this->writeToLog("Player " . $playerID . " has built a road on tile (" . $x . "," . $y . "), position: " . $buildPosition);
			$this->createGameXML();
		}
		
		// Position can be topLeft, topRight, bottomLeft, or bottomRight.
		// Need to check if possible to build before building (since it cost resources.
		public function buildSettlement($playerID, $x, $y, $buildPosition)
		{
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
			$this->createGameXML();
			$this->checkWinningConditions();
		}
		
		// Position can be topLeft, topRight, bottomLeft, or bottomRight.
		// Need to check if possible to build before building (since it cost resources.
		public function buildCity($playerID, $x, $y, $buildPosition)
		{
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
			return in_array($playerID, $this->playersByID);
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