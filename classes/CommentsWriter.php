<?php

/********************************************************************
 *
 *  File's name: CommentsWriter.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 11.11.2014r.
 *  Last modificated: 04.12.2014r.
 *
 ********************************************************************/
 
class CommentsWriter implements CommentInterface { // prawdopodobnie jest blad w nazwie ze wzgledu na gramatyke angielska, to nie jest klasa zapisujaca komentarze prawda?
	protected $comments = array(); // czy na pewno musisz uzyc protected? uzywaj minimalnej widocznosci, tutaj zdaje sie wystarczy private
	
	public function __construct($comments = array()) {
		if(!is_array($comments)) throw new CommentException('@param $comments musi być tablicą.');		
		
		foreach($comments as $Comment) {
			$this->addComment($Comment);	
		}
	}

	public function addComment(Comment $Comment) {
		$this->comments[] = $Comment;	
	}

	public function getQuantity() {
		return count($this->comments);	
	}

	/**
	 *
	 * Modifies all the comments in the object, using their own methods to do it.
	 *
	 * Returns the quantity of modified comments.
	 *
	**/
	public function edit() {
		$count = 0;
		
		foreach($this->comments as $Comment) {
			$count += $Comment->edit();
		}
	
		return $count;
	}
	
	/**
	 *
	 * Deletes all the comments in the object.
	 *
	 * Returns the quantity of deleted comments.
	 *
	**/
	public function delete() {
		$query = "DELETE FROM ".COMMENTS_TABLE." WHERE id = :id";
				
		$PDO = DataBase::getInstance();
		$Operation = $PDO->prepare($query);
		
		$count = 0;
			
		foreach($this->comments as $Comment) {
			$Operation->bindValue(':id', $Comment->getId(), PDO::PARAM_INT);
			
			$count += $Operation->execute();	
		}
	
		return $count;
	}

	/**
	 *
	 * Creates a form with all the comments. It can be used to edit them.
	 *
	 * Returns the string with HTML code of the form.
	 *
	**/
	public function getForm() {
		$Link = Link::create();		
		
		$form = "\n<form class=\"rt_form rt_comment_form rt_comment_group_form\" action=\"{$Link->get()}\" method=\"post\">";		
			
			foreach($this->comments as $Comment) {
			$form .= "\n\n\t<fieldset>";
				$form .= "\n\t\t<input name=\"data[{$Comment->getId()}][name]\" type=\"hidden\" value=\"{$Comment->getName()}\" />";
				$form .= "\n\t\t<input name=\"data[{$Comment->getId()}][id]\" type=\"hidden\" value=\"{$Comment->getId()}\" />";	
				
				$form .= "\n\n\t\t<div class=\"rt_user_informations\">";
					
					if($Comment instanceof PlainComment) {
						$form .= "\n\t\t\t<label>";
							$form .= "\n\t\t\t\tNick:<span>*</span>";
							$form .= "\n\t\t\t\t<input name=\"data[{$Comment->getId()}][nick]\" type=\"text\" value=\"{$Comment->getNick()}\" />";
						$form .= "\n\t\t\t</label>";
						
						$form .= "\n\t\t\t<label>";
							$form .= "\n\t\t\t\tAdres e-mail:";
							$form .= "\n\t\t\t\t<input name=\"data[{$Comment->getId()}][email]\" type=\"text\" value=\"{$Comment->getEmail()}\" />";
						$form .= "\n\t\t\t</label>";
						
						$form .= "\n\t\t\t<label>";
							$form .= "\n\t\t\t\tStrona internetowa:";
							$form .= "\n\t\t\t\t<input name=\"data[{$Comment->getId()}][www]\" type=\"text\" value=\"{$Comment->getWww()}\" />";
						$form .= "\n\t\t\t</label>";
					}
					else {
						$form .= "\n\t\t\t<p>{$Comment->getNick()}</p>";
						$form .= "\n\t\t\t<input name=\"data[{$Comment->getId()}][admin_id]\" type=\"hidden\" value=\"{$Comment->getUser()->getId()}\" />";
						$form .= "\n\t\t\t<input name=\"data[{$Comment->getId()}][login]\" type=\"hidden\" value=\"{$Comment->getNick()}\" />";
					}					
				$form .= "\n\t\t</div>";
				
				$form .= "\n\n\t\t<label class=\"rt_content\">";
					$form .= "\n\t\t\tTreść:<span>*</span>";
					$form .= "\n\t\t\t<textarea name=\"data[{$Comment->getId()}][content]\">".DeleteBrs($Comment->getContent())."</textarea>";
				$form .= "\n\t\t</label>";	
			$form .= "\n\t</fieldset>";
			}
		
			$form .= "\n\n\t\t<button type=\"submit\">OK</button>";
			$form .= "\n\n\t\t<a class=\"rt_return\" href=\"".$_SESSION['return']."\">&lt;&lt; Powrót</a>";
		$form .= "\n</form>";
		
		return $form;
	}
	
	/**
	 *
	 * @param $u Boolean - if the admin is loged in - true, in the other case - false
	 *
	 * Returns HTML code of the list, which presents the comments.
	 *
	**/
	public function get($u = false) {
		$list = "\n\n<ul class=\"rt_comments\">";
		
		foreach($this->comments as $Comment) {
			$list .= "\n\t<li>";			
				
			$Comment->setAdminsFlag($u);
			$list .= $Comment;
			
			$list .= "\n\t</li>";
		}				
			
		$list .= "\n</ul>";	
		
		return $list;
	}
	
	/**
	 *
	 * Returns a form in the shape of a table. It is possible to choose the comments to edit or delete.
	 *
	**/
	public function getAsTable() {
		$form = "\n<form class=\"rt_comments_table\" action=\"".PANEL_FILE."\" method=\"get\">";
		$table = "\n\t<table>";
			$table .= "\n\t\t<tr>";
				$table .= "\n\t\t\t<th>X</th><th>Miejsce</th><th>Autor</th><th>Data dodania</th><th>Treść</th>";
			$table .= "\n\t\t</tr>";
			
			foreach($this->comments as $Comment) {
				$table .= ($Comment instanceof AdminComment) ? "\n\t<tr class=\"rt_admin_comment\">" : "\n\t<tr>";
					$table .= "\n\t\t\t<td valign=\"top\"><input name=\"id[]\" type=\"checkbox\" value=\"{$Comment->getId()}\" /></td>";
					$table .= "\n\t\t\t<td valign=\"top\">{$Comment->getName()}</td>";
					$table .= "\n\t\t\t<td class=\"rt_nick\" valign=\"top\">{$Comment->getNick()}</td>";
					$Date = $Comment->getDate();
					$date = $Date->getShortly().", ".$Date->getTime();
					$table .= "\n\t\t\t<td class=\"rt_date\" valign=\"top\">{$date}</td>";
					$table .= "\n\t\t\t<td class=\"rt_content\" valign=\"top\">{$Comment->getContent()}</td>";
				$table .= "\n\t\t</tr>";	
			}
		$table .= "\n\t</table>";
		
		$form .= $table;
		$form .= "\n\n\t<button id=\"rtEditButton\" name=\"action\" value=\"edit\">Edytuj zaznaczone</button>";
		$form .= "\n\n\t<button id=\"rtDeleteButton\" name=\"action\" value=\"delete\">Usuń zaznaczone</button>";
		$form .= "\n</form>";		
		
		return $form;
	}
	
	/**
	 *
	 * @param $id Array - an array with identifiers of comments to load
	 *
	 * Loads the comments with identifiers given in the argument.
	 *
	 * Returns itself own object with these comments.
	 *
	**/
	static public function create($id) {
		$Factory = new CommentsFactory();
		$Factory->setIds($id);
		$Factory->setOrder(false);
		
		return $Factory->get();
	}
	
	/**
	 *
	 * @param $name String - a name of the group of comments to load
	 *
	 * Loads the comments belonging to this group.
	 *
	 * Returns itself own object with these comments.
	 *
	**/
	static public function createByName($name) {
		$Factory = new CommentsFactory();
		$Factory->setName($name);
		$Factory->setOrder(false);
		
		return $Factory->get();
	}
} 
 
?>