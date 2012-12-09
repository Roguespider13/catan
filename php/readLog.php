<?php
    require_once 'logManager.php';
    
    $logFile = $_GET['file'];
    $logMan= new LogManager();
    
    if ($logMan->doesCompletedLogExist($logFile)) {
        $lines = file("./logs/$logFile.log");
        
        foreach ($lines as $line) {
            echo "<span style=\"font-size: 14pt; font-family: 'Comic Sans MS', cursive, sans-serif;\">" . htmlentities($line) . "</span><br />";
        }
    }
?>