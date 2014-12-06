<?php

/********************************************************************
 *
 *  File's name: panel.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 13.11.2014r.
 *  Last modificated: 27.11.2014r.
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
	<title>Panel administracyjny | System komentarzy</title>
</head>
<body>

<?php

if($U = User::getInstance()) {
	try {
		$action = (isset($_GET['action'])) ? $_GET['action'] : null;
		$id = (isset($_GET['id'])) ? $_GET['id'] : null;
		$name = (isset($_GET['name'])) ? $_GET['name'] : null;

		$Link = new Link(PANEL_FILE);			
		$Current = Link::create();		
		
		echo "\n<div class=\"rt_container\">";		
		echo "\n\t<header>";
		echo "\n\t\t<p class=\"rt_last_visit\">Ostatnia wizyta: {$U->getLastVisit()->getDate()}</p>";	
		echo "\n\t\t<h1><a href=\"{$Link->get()}\">Panel administracyjny</a></h1>";
		echo "\n\n\t\t<nav>";
		echo "\n\t\t\t<ul>";
		$Link->setParameter('action', 'comments');
		echo "\n\t\t\t\t<li><a href=\"{$Link->get()}\">Komentarze</a></li>";
		echo "\n\t\t\t</ul>";
		echo "\n\t\t</nav>";
		echo "\n\t</header>";		
		
		echo "\n\n\t<main>";		
		
		switch($action) {	
			
			case 'edit':
				if($_SERVER['REQUEST_METHOD'] == 'POST') {
					$Date = Date::create(date('Y-m-d H:i:s'));					
					
					if(isset($_POST['data'])) {
						$Comment = new CommentsWriter();
						
						foreach($_POST['data'] as $data) {
							$id = $data['id'];
							$name = $data['name'];
							$content = nl2br(trim($data['content']));							
							
							if(isset($data['admin_id'])) {
								$admin_id = $data['admin_id'];	
								$login = $data['login'];
								$U = new User($admin_id, $login);								
								
								$C = new AdminComment($id, $name, $U, $content, $Date);						
							}
							else {
								$nick = trim($data['nick']);
								$email = trim($data['email']);
								$www = trim($data['www']);
								
								$nick = htmlspecialchars($nick);
								$email = (!empty($email)) ? $email : null;
								$www = (!empty($www)) ? AddHttp($www) : null;
								
								$C = new PlainComment($id, $name, $nick, $email, $www, $content, $Date);
							}
						
							$Comment->addComment($C);
						}
					}
					else {
						$id = $_POST['id'];
						$name = trim($_POST['name']);		
						$content = nl2br(htmlspecialchars(trim($_POST['content'])));				
	
						if(isset($_POST['admin_id']) && $_POST['admin_id'] > 0) {
							$nick = trim($_POST['login']);
							$U = new User($_POST['admin_id'], $nick);			
								
							$Comment = new AdminComment($id, $name, $U, $content, $Date);	
						}
						else {
							$nick = (isset($_POST['nick'])) ? htmlspecialchars(trim($_POST['nick'])) : null;		
							$email = (isset($_POST['email']) && !empty($_POST['email'])) ? trim($_POST['email']) : null;
							$www = (isset($_POST['www']) && !empty($_POST['www'])) ? AddHttp(htmlspecialchars(trim($_POST['www']))) : null;				
			
							$Comment = new PlainComment($id, $name, $nick, $email, $www, $content, $Date);
						}
					}
				
					if($quantity = $Comment->edit()) {
						if($Comment instanceof CommentsWriter) {
							if($quantity == $Comment->getQuantity()) {
								echo Correct("Pomyślnie zmodyfikowano wszystkie komentarze!");	
							}
							else {
								echo Error("Wystąpiły problemy podczas modyfikacji ".($Comment->getQuantity() - $quantity)." komentarzy. Mogły nie zostać zmodyfikowane. Proszę spróbować później.");
							}
						}
						else {
							echo Correct("Pomyślnie zmodyfikowano komentarz!");
						}	
					}
					else {
						echo Error("Wystąpiły problemy z edycją komentarza&hellip; Proszę spróbować ponownie później.");	
					}
				}
				else {
					echo "\n<h2>Edytowanie</h2>";					
					
					$Comment = (IsIntegerArray($id)) ? CommentsWriter::create($id) : Comment::create($id);
					
					echo $Comment->getForm();
				}
			break;			
			
			case 'delete':
				if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['decision']) && $_POST['decision'] == 1) {
					
					$Comment = (IsIntegerArray($id)) ? CommentsWriter::create($id) : Comment::create($id);
				
					if(!$Comment) throw new CommentException('Nie istnieje komentarz o podanym numerze id.');				
				
					if($quantity = $Comment->delete()) {
						if($Comment instanceof CommentsWriter) {
							if($quantity == $Comment->getQuantity()) {
								echo Correct("Wszystkie {$quantity} komentarze zostały usunięte pomyślnie!");
							}
							else {
								echo Error("Wystąpiły problemy podczas usuwania komentarzy. ".($Comment->getQuantity() - $quantity)." z nich nie zostały usunięte. Proszę spróbować później.");	
							}
						}
						else {
							echo Correct("Komentarz został usunięty pomyślnie!");
						}
					}
					else {
						echo Error("Wystąpiły problemy podczas usuwania. Proszę spróbować ponownie później.");	
					}
				}
				else {
					$title = (IsIntegerArray($id)) ? 'Czy na pewno chcesz usunąć wszystkie zaznaczone komentarze?' : 'Czy na pewno chcesz usunąc ten komentarz?';					
					
					$form = "<form class=\"rt_form rt_delete_form\" action=\"".$Current->get()."\" method=\"post\">";
						$form .= "\n\t<fieldset>";
							$form .= "\n\t\t<h2>{$title}</h2>";
							$form .= "\n\n\t\t<input name=\"decision\" type=\"hidden\" value=\"1\" />";
							$form .= "\n\n\t\t<button type=\"subject\">Tak</button>";
							$form .= "\n\t\t<a class=\"rt_return\" href=\"".$_SESSION['return']."\">Nie</a>";
						$form .= "\n\t</fieldset>";
					$form .= "\n</form>";
					
					echo $form;
				}
			break;
					
			case 'comments':
				echo "\n<h2>Komentarze</h2>";			
			
				$PDO = DataBase::getInstance();
				
				$list = "\n\n<ul class=\"rt_comments_groups\">";
								
				$query = "SELECT COUNT(id) as quantity FROM ".COMMENTS_TABLE;
				$result = $PDO->query($query)->fetch();
				
				$Link->setParameter('action', 'show_comments');
				$list .= "\n\t<li class=\"rt_all_link\"><a href=\"".$Link->get()."\">Wszystkie (".$result['quantity'].")</a></li>";
				
				$query = "SELECT DISTINCT name as unique_name, (SELECT COUNT(id) FROM ".COMMENTS_TABLE." WHERE name = unique_name) as quantity FROM ".COMMENTS_TABLE." ORDER BY name ASC";
				$Operation = $PDO->query($query);				
				
				foreach($Operation as $result) {
					$Link->setParameter('action', 'show_comments');
					$Link->setParameter('name', $result['unique_name']);
					$list .= "\n\t<li><a href=\"".$Link->get()."\">".$result['unique_name']." (".$result['quantity'].")</a></li>";
				}				

				$list .= "\n</ul>";
				
				echo $list;
			break;		
			
			case 'show_comments':
				$Comments = CommentsWriter::createByName($name);
				
				echo $Comments->getAsTable();
			break;			
			
			default:
				echo "<h2>Witaj w panelu administratora, ".$U->getLogin()."!</h2>";
				
				$Link->setParameter('id', 'comments');
				echo "\n\n<p>\nMożesz tutaj zarządzać dodawanymi przez użytkowników komentarzami. W dziale <a href=\"".$Link->get()."\">Komentarze</a> znajduje się lista z wszystkimi miejscami, gdzie dodano jakieś wpisy. Klikając w link na znajdującej się tam liście zostaniesz przeniesiony na stronę, gdzie będziesz mógł przejrzeć komentarze dodane właśnie na niej i usunąć lub edytować wybrane. Poza tym komentarze dodane do systemu od czasu Twojej ostatniej wizyty w panelu administratora są oznaczone specjalnym kolorem, byś od razu mógł rozpoznać te nowe, jeszcze nie przejrzane.\n</p>";
				
				$Link->setParameter('id', 'settings');
				echo "\n\n<p>\nNatomiast w dziale <a href=\"".$Link->get()."\">Ustawienia</a> możesz dokokać drobnych zmian w konfiguracji systemu. Póki co jest tam możliwość zmiany ilości komentarzy wyświetlanych na jednej stronie w panelu administratora. Możesz tam także zmienić ilość sekund, ile musi poczekać uzytkownik by ponownie dodać komentarz w tym samym miejscu, żeby uniemożliwić próby &bdquo;ręcznego&rdquo; spamu.\n</p>";
				
				echo "\n\n<p>\nMam nadzieję, że ten system komentarzy przyda się na Twojej stronie, a wszelkie funkcjonalności są intuicyjne, sprawne i wystar&shy;czające, by zarządzać nawet sporą ilością komentarzy. Gdybyś miał jakieś sugestie, jak ulepszyć ten skrypt, albo co można jeszcze dodać, by praca z&nbsp;komentarzami była sprawniejsza, napisz, proszę, do mnie e-mail.\n</p>";
				
				echo "\n\n<p class=\"rt_signature\">\n<a href=\"http://www.filipmarkiewicz.pl\">Autor</a>\n</p>";
			break;
		}
	
		echo "\n\n<a class=\"rt_return\" href=\"/\">&lt;&lt;</a>";
		echo "\n</main>";
		
		if(!$U->updateLastVisit()) echo Error("Wystąpił problem z aktualizacją daty ostatniej wizyty w panelu.");
	}
	catch(CommentException $CE) {
		echo Error($CE->getMessage());
	}
	catch(Exception $E) {
		
	}
}
else {
	echo Error("Nie masz dostępu do panelu administracyjnego. Proszę się uprzednio zalogować, by ów dostęp uzyskać.");	
} 
 
?>

</body>
</html>