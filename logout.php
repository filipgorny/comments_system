<?php

/********************************************************************
 *
 *  File's name: logout.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 12.11.2014r.
 *  Last modificated: 12.11.2014r.
 *
 ********************************************************************/

session_start();

include('functions.php');
include('config.php');

?>
<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<script src="/js/html5shiv.js"></script>
	<link rel="stylesheet" href="style.css" />
	<title>Logowanie | System komentarzy</title>
</head>
<body>

<?php

if(isset($U)) {
	if($U->logout())
	{
		echo Correct("Wylogowano poprawnie!");	
	}
	else {
		echo Error("Wystąpiły problemy podczas wylogowywania.");	
	}
}
else {
	echo Error("Musisz się uprzednio zalogować, by móc się wylogować&hellip;");	
} 
 
?>

</body>
</html>