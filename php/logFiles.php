<!DOCTYPE html>
<?php
    require_once 'force_authentication.php';
    //require_once 'gameManager.php';
?>
<html>
<head>
    <title>eCatan - Simple Settlers</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel = "stylesheet" type = "text/css" href = "../css/bootstrap.css" media = "all">
    <link href='http://fonts.googleapis.com/css?family=Carrois+Gothic'
            rel='stylesheet' type='text/css'>

    <style type="text/css">

        body {
            padding-top: 60px;
            padding-bottom: 40px;
            background:url("../images/bg.png");
        }

        .navbar {
            box-shadow:0 2px 3px #b1b4e7;
        }

        .logs {
            max-width: 300px;
            padding: 19px 29px 29px;
            margin: 0 auto 20px;
            background-color: #fff;
            border: 1px solid #e5e5e5;
            -webkit-border-radius: 5px;
               -moz-border-radius: 5px;
                    border-radius: 5px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
               -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                    box-shadow: 0 1px 2px rgba(0,0,0,.05);
        }
    </style>
</head>

<body>
  <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <a class="brand" href="#">eCatan</a>
        <div class="nav-collapse collapse">
          <ul class="nav">
            <li class=""><a href="create_or_join.php">Home</a></li>
            <li class="active"><a href="#">Logs</a></li>
            <li><a href="stats.php">Stats</a></li>
            <li><a href="signout.php">Signout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

    <div id="container-fluid">
        <div class="row-fluid">
            <div class="logs">
                <?php
                    //require_once 'Game.php';
                    require_once 'logManager.php';

                    $logMan= new LogManager();
                    $logs = $logMan->getCompletedLogs();
                    //$logMan->doesCompletedLogExist($gameID);

                    echo "<h3>Available log files</h3><ul>";
                    foreach ($logs as $log) {
                        //$logFile = $logMan->getCompletedLogFile($log);
                        //if (strlen($logFile) > 0) {
                            echo "<li><a href=\"readLog.php?file=" . htmlentities($log) . "\">" . htmlentities($log) . "</a></li>";
                            //echo "<li><a href=\"" . htmlentities($logFile) . "\">" . htmlentities($log) . "</a></li>";
                        //}
                    }
                    echo "</ul>";
                ?>
            </div>
        </div>
    </div>
</body>