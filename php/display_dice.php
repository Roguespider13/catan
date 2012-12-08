<?php
	switch ($_SESSION['DIE1']) {
		case "1":
			echo "<img src = \"../images/die1.jpg\">&nbsp;&nbsp;";
			break;
		case "2":
			echo "<img src = \"../images/die2.jpg\">&nbsp;&nbsp;";
			break;
		case "3":
			echo "<img src = \"../images/die3.jpg\">&nbsp;&nbsp;";
			break;
		case "4":
			echo "<img src = \"../images/die4.jpg\">&nbsp;&nbsp;";
			break;
		case "5":
			echo "<img src = \"../images/die5.jpg\">&nbsp;&nbsp;";
			break;
		case "6":
			echo "<img src = \"../images/die6.jpg\">&nbsp;&nbsp;";
			break;
		case 1:
			// Do nothing
			break;
	}

	switch ($_SESSION['DIE2']) {
		case "1":
			echo "<img src = \"../images/die1.jpg\">";
			break;
		case "2":
			echo "<img src = \"../images/die2.jpg\">";
			break;
		case "3":
			echo "<img src = \"../images/die3.jpg\">";
			break;
		case "4":
			echo "<img src = \"../images/die4.jpg\">";
			break;
		case "5":
			echo "<img src = \"../images/die5.jpg\">";
			break;
		case "6":
			echo "<img src = \"../images/die6.jpg\">";
			break;
		case 1:
			// Do nothing
			break;
	}
?>
