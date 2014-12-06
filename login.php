<?php

/********************************************************************
 *
 *  File's name: login.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 11.11.2014r.
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

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if($User = User::login($_POST['login'], $_POST['password'])) {
		$_SESSION['user']['id'] = $User->getId();
		$_SESSION['user']['login'] = $User->getLogin();
		
		echo Correct("Zalogowano pomyślnie.");
	}
	else {
		echo Error("Podano nieprawidłowe dane logowania.", $Current->get());
	}
}
else {
	$form = "<form class=\"rt_form\" action=\"".LOGIN_FILE."\" method=\"post\">";
		$form .= "\n\t<fieldset>";
			$form .= "\n\t\t<h2>Logowanie</h2>";

			$form .= "\n\n\t\t<label>";
				$form .= "\n\t\t\tLogin:";
				$form .= "\n\t\t\t<input name=\"login\" type=\"text\" />";
			$form .= "\n\t\t</label>";
			
			$form .= "\n\n\t\t<label>";
				$form .= "\n\t\t\tHasło:";
				$form .= "\n\t\t\t<input name=\"password\" type=\"password\" />";
			$form .= "\n\t\t</label>";
			
			$form .= "\n\n\t\t<button type=\"submit\">Zaloguj</button>";
			$form .= "\n\n\t\t<a class=\"rt_return\" href=\"".$_SESSION['return']."\">&lt;&lt; Powrót</a>";
		$form .= "\n\t</fieldset>";
	$form .= "\n</form>";
	
	echo $form;
}  
 
?>

</body>
</html>