<?php

class LogManager
{
	private $onGoingGameFolder = "games/";
	private $completedGameFolder = "logs/";

	private $onGoingGameLogs;
	private $completedGameLogs;

	public function __construct()
	{
		$this->onGoingGameLogs = array();
		$this->completedGameLogs = array();

		$gameLogFiles = scandir($this->onGoingGameFolder);
		foreach ($gameLogFiles as $gameLogFile)
		{
			//echo $gameFile . "<br>";
			if (is_dir($gameLogFile))
				continue;
			$fileInfo = pathinfo($gameLogFile);
			$gameID = $fileInfo["filename"];
			$logFile = $fileInfo["basename"];

			if ($fileInfo["extension"] == "log")
			{
				$this->onGoingGameLogs[$gameID] = $this->onGoingGameFolder . $logFile;
			}
		}

		$completedGameLogFiles = scandir($this->completedGameFolder);
		foreach ($completedGameLogFiles as $gameLogFile)
		{
			//echo $gameFile . "<br>";
			if (is_dir($gameLogFile))
				continue;
			$fileInfo = pathinfo($gameLogFile);
			$gameID = $fileInfo["filename"];
			$logFile = $fileInfo["basename"];

			if ($fileInfo["extension"] == "log")
			{
				$this->completedGameLogs[$gameID] = $this->completedGameFolder . $logFile;
			}
		}
	}

	public function createGameLogFile($gameID)
	{
		if ($this->doesOngoingLogExist($gameID))
			throw new Exception("Current Log exists for that game.");

		$logFileName = $this->onGoingGameFolder . $gameID . ".log";
		$logHandle = fopen($logFileName, "w");
		fwrite($logHandle, "LOG STARTED FOR GAME: " . $gameID . "\n");
		fclose($logHandle);
		$this->onGoingGameLogs[$gameID] = $logFileName;

		return $logFileName;
	}

	public function doesOngoingLogExist($gameID)
	{	return array_key_exists($gameID, $this->onGoingGameLogs);	}

	public function doesCompletedLogExist($gameID)
	{	return array_key_exists($gameID, $this->completedGameLogs);	}

	public function getOngoingLogFile($gameID)
	{
		if ( $this->doesOngoingLogExist($gameID))
			return $this->onGoingGameLogs[$gameID];
		return "";
	}

	public function getCompletedLogFile($gameID)
	{
		if ($this->doesCompletedLogExist($gameID))
			return $this->completedGameLogs[$gameID];
		return "";
	}

	// Close will close the log file permanently and should only  be used when the game is completed.
	public function closeOngoingLog($gameID)
	{
		if (! $this->doesOngoingLogExist($gameID))
			return "";

		if (copy($this->onGoingGameLogs[$gameID], $this->completedGameFolder . $gameID . ".log"))
		{
			unlink($this->onGoingGameLogs[$gameID]);
			if(($key = array_search($gameID, $this->onGoingGameLogs)) !== false)
			{
				unset($this->onGoingGameLogs[$key]);
			}
			return $this->completedGameFolder . $gameID . ".log";
		}
		else
			return "";
	}

}
?>
