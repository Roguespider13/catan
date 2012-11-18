<?php

class UserManager
{
	
	private $userFolder = "/users/";
	private $currentUserFiles;

	public function __construct() {
		$this->$currentUserFiles = array();
		foreach(glob($this->userFolder . "/*.usr") as $filename){
			$this->$currentUserFiles[] = $filename;
		}
		
		sort($this->currentUserFiles);		
	}
	
	public function doesUserExist($userName)
	{	return in_array($userName . ".usr", $this->currentUserFiles);	}
	
	public function createUser($userName, $password)
	{
		if ($this->doesUserExist($userName))
			throw new Exception("User already exists");
		
		$usrHandle = fopen($this->userFolder . $userName . ".usr");
		$salt = $this->generateSalt(16);
		$secureHash = $this->generateSecureHash($password, $salt);
		// Username:SALT:hash:Wins:Loses
		fwrite($usrHandle, $userName . ":" . $salt . ":" . $secureHash . ":0:0\n");
		fclose($usrHandle);		
	}
	
	public function authenticateUser($username, $password)
	{
		if (! $this->doesUserExist($username))
			return false;
		
		return ($this->generateSecureHash($password, "$6$rounds=5000$" . $this->getSalt($userName) . "$" == $this->getSecureHash($userName)));
		
	}
	
	/* Positions:
	 * 0 = username
	 * 1 = salt
	 * 2 = hash
	 * 3 = wins
	 * 4 = loses
	*/	
	private function getUserElement($userName, $position)
	{
		$fileContents = fopen($this->userFolder . $username . ".usr");
		$userContents = $fileContents[0];
		$elements = explode(":", $userContents);
		
		return $elements[$position];
	}
			
	private function getSecureHash($userName)
	{	return $this->getUserElement($userName, 2);	}
	
	private function getSalt($userName)
	{	return $this->getUserElement($userName, 1);	}
	
	public function getUserWins($userName)
	{	return $this->getUserElement($userName, 3);	}
	
	public function getUserLoses($userName)
	{	return $this->getUserElement($userName, 4); }
	
	public function addUserWin($userName)
	{
		$fileContents = fopen($this->userFolder . $username . ".usr");
		$userContents = explode(":", $fileContents[0]);
		$wins = intval($userContents[3]);
		$userContents[3] = strval($wins + 1);
		
		$usrHandle = fopen($this->getUserFileName($userName));
		fwrite($usrHandle, $userContents[0] . ":" . $userContents[1] . ":" . $userContents[2] . ":" . $userContents[3] . ":" . $userContents[4]);
	}
	
	public function addUserLoss($userName)
	{
		$fileContents = fopen($this->userFolder . $username . ".usr");
		$userContents = explode(":", $fileContents[0]);
		$losses = intval($userContents[4]);
		$userContents[3] = strval($losses + 1);
		
		$usrHandle = fopen($this->getUserFileName($userName));
		fwrite($usrHandle, $userContents[0] . ":" . $userContents[1] . ":" . $userContents[2] . ":" . $userContents[3] . ":" . $userContents[4]);
	}
	
	private function getUserFileName($userName)
	{	return $this->userFolder . $userName . ".usr";	}
	
	private function generateSalt($max = 32) {
		$baseStr = time() . rand(0, 1000000) . rand(0, 1000000);
		$md5Hash = md5($baseStr);
		if($max < 32){
			$md5Hash = substr($md5Hash, 0, $max);
		}
		return $md5Hash;
	}
	
	private function generateSecureHash($password, $salt)
	{
		return crypt($password, "$6$rounds=5000$" . $salt . "$");
				
	}
	
}
?>
