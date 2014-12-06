<?php

/********************************************************************
 *
 *  File's name: add.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 10.11.2014r.
 *  Last modificated: 06.12.2014r.
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
	<title>System komentarzy</title>
</head>
<body>

<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	try {
		$name = trim($_POST['name']);		
		$content = nl2br(htmlspecialchars(trim($_POST['content'])));		
		$Date = Date::create(date('Y-m-d H:i:s'));		
		
		if(isset($_POST['admin_id']) && $_POST['admin_id'] > 0) {
			$nick = trim($_POST['login']);
			$U = new User($_POST['admin_id'], $nick);			
			
			$Comment = new AdminComment(null, $name, $U, $content, $Date);	
		}
		else {
			$nick = (isset($_POST['nick'])) ? htmlspecialchars(trim($_POST['nick'])) : null;		
			$email = (isset($_POST['email']) && !empty($_POST['email'])) ? trim($_POST['email']) : null;
			$www = (isset($_POST['www']) && !empty($_POST['www'])) ? AddHttp(htmlspecialchars(trim($_POST['www']))) : null;
			
			$errors = array();			
			
			if($_POST['result'] != $_POST['user_result']) {
				$errors[] = "Proszę poprawnie wykonać działanie.";	
			}		
		
			if(strlen($nick) < 1) {
				$errors[] = "Proszę się przedstawić.";	
			}
			if(strlen($nick) > 40) {
				$errors[] = "Proszę podać nick krótszy niż 40 znaków.";	
			}
			if(!empty($email) && !CheckEmail($email)) {
				$errors[] = "Proszę wprowadzić poprawny adres poczty elektronicznej.";	
			}
			if(!empty($www) && !CheckWebsite($www)) {
				$errors[] = "Proszę podać poprawny adres swojej strony internetowej.";	
			}	
			if(strlen($content) < 5) {
				$errors[] = "Proszę napisać coś treściwszego.";
			}
			if(strlen($content) > 65535) {
				$errors[] = "Podziwiam Twój zapał literacki, jednak Twój wpis jest zbyt obszerny. Proszę napisać coś krótszego niż 65 535 znaków. Oto wpisany przed chwilą tekst:<br />".$content;	
			}
			
			if(count($errors) > 0) {
				$warning = null;
				
				foreach($errors as $error) {	
					$warning .= $error."<br />";
				}	
				
				throw new CommentException($warning);
			}				
			
			$Comment = new PlainComment(null, $name, $nick, $email, $www, $content, $Date);
		}
	
		if($Comment->add()) {
			echo Correct("Komentarz został dodany pomyślnie!");
			
			$message = "Uzytkownik o nick'u {$nick} dodał komentarz do grupy '{$name}' o następującej treści:\n".DeleteBrs($content);			
			
			$M = new Mailer('Nowy komentarz na stronie!', $message);
			
			foreach($admin_emails as $email) {
				$M->addRecipient($email);	
			}
		
			$M->send();
		}
		else {
			throw new CommentException("Wystąpiły problemy podczas dodawania komentarza. Proszę spróbować później.");
		}
	}
	catch(CommentException $CE) {
		echo Error($CE->getMessage());	
	}
	catch(Exception $E) {
		$Mail = new Mailer('Błąd na stronie: '.$_SERVER['SERVER_NAME'], $E->getMessage());
		$Mail->send();
		
		echo Error("Wystąpił nieoczekiwany błąd. Administrator został już poinformowany o problemach. Proszę spróbowac ponownie za jakiś czas.<br />".$E->getMessage());
	}
}
else {
	Error("Nie wysłano formularza.");
}  
 
?>

</body>
</html>