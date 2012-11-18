<?php
    require_once 'boardLayout.php';
    require_once 'player.php';
    require_once 'InputValidator.php';
    
    // Each game has a creator, two participants, and a board layout
    class Game {
        // Name of the game creator as entered when creating the game
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
        private $boardLayout;
        
        // InputValidator object
        private $validator;
        
        public function createGame($creatorName, $gameID, $player2) {
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
            $this->boardLayout = new BoardLayout();
			$this->boardLayout->createLayout();
			
			$this->createGameXML();
        }
		

		private function createGameXML()
		{
			$xmlFileName = GameManager::getGameXML($this->gameID);
			
			$xmlDoc = new DOMDocument('1.0');
			$rootNode = $xmlDoc->createElement("CatanGame");
			$xmlDoc->appendChild($rootNode);
			
			$gameNumXML = $xmlDoc->createElement("GameNumber");
			$gameNumText = $xmlDoc->createTextNode($this->gameID);
			$gameNumXML->appendChild($gameNumText);
			$rootNode->appendChild($gameNumXML);
			
			$playersXML = $xmlDoc->createElement("Players");
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
			$this->player1 = Player($playersXML[0]->attributes()->id);
			$this->player1->reconstruct($playersXML[0]);
			
			$this->player2 = Player($playersXML[1]->attributes()->id);
			$this->player2->reconstruct($playersXML[1]);
			
			$this->boardLayout = BoardLayout::reconstructLayout($gameID->GameBoard);
		}
		
		public function performDieRoll()
		{	return rand(1, 6);	}
		
		public function setDice($die1, $die2)
		{
			if ($die1 > 6 || $die1 < 1 || $die2 > 6 || $die2 < 1)
				throw new Exception("Invalid Dice Numbers");
			
			$resTiles = $this->boardLayout->getTilesMatchingRoll($die1 + $die2);
			
			foreach($resTiles as $resTile)
			{
				
			}
			
			
		}
		
/*        private function SetGameName($gameName) {
            $this->gameName = $gameName;
        }
 
 * 
 */ 
    }
 
?>