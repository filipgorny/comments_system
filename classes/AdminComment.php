<?php

/********************************************************************
 *
 *  File's name: AdminComment.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 09.11.2014r.
 *  Last modificated: 06.12.2014r.
 *
 ********************************************************************/

class AdminComment extends Comment {
	protected $User;
	
	public function __construct($id, $name, User $User, $content, Date $Date) {
		parent::__construct($id, $name, $content, $Date);		
		
		$this->User = $User;
	}	
	
	public function __toString() {
		$comment = "\n\t\t<div class=\"rt_comment rt_admin_comment\">";
		
		if($this->isAdminLoged()) {
			$comment .= "\n\t\t\t<ul class=\"rt_admin_actions\">";
				$comment .= "\n\t\t\t\t<li><a href=\"".$this->getEditPath()."\">[Edytuj]</a>";	
				$comment .= "\n\t\t\t\t<li><a href=\"".$this->getDeletePath()."\">[Usuń]</a>";	
			$comment .= "\n\t\t\t</ul>";
		}
	
			$comment .= "\n\t\t\t<div class=\"rt_data\">";
				$comment .= "\n\t\t\t\t<p class=\"rt_nick\">{$this->getNick()}</p>";
				$comment .= "\n\t\t\t\t<p class=\"rt_date\">{$this->getDate()->getDate()}</p>";
			$comment .= "\n\t\t\t</div>";
			
			$comment .= "\n\n\t\t\t<p class=\"rt_content\">";
				$comment .= "\n\t\t\t{$this->getContent()}";
			$comment .= "\n\t\t\t</p>";	
		$comment .= "\n\t</div>";
	
		return $comment;
	}	
	
	/**
	 *
	 * Returns user's login
	 *
	**/
	public function getNick() {
		return $this->User->getLogin();	
	}	
	
	/**
	 *
	 * Returns User object.
	 *
	**/
	public function getUser() {
		return $this->User;	
	}	
	
	/**
	 *
	 * Adds this comment to the database.
	 *
	 * Returns true or false.
	 *
	**/
	public function add() {
		$date = date('Y-m-d H:i:s');
		$query = "INSERT INTO ".COMMENTS_TABLE." VALUES(null, :name, null, null, null, :content, :date, :admin_id)";		
		
		$PDO = DataBase::getInstance();		
		
		$Operation = $PDO->prepare($query);
		$Operation->bindValue(':name', $this->name, PDO::PARAM_STR);
		$Operation->bindValue(':content', $this->content, PDO::PARAM_STR);
		$Operation->bindValue(':date', $date, PDO::PARAM_STR);
		$Operation->bindValue(':admin_id', $this->User->getId(), PDO::PARAM_STR);
		
		if($Operation->execute()) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 * Modifies the comment.
	 *
	 * Returns true or false.
	 *
	**/
	public function edit() {
		$query = "UPDATE ".COMMENTS_TABLE." SET content = :content WHERE id = :id";
		
		$PDO = DataBase::getInstance();		
		
		$Operation = $PDO->prepare($query);
		$Operation->bindValue(':content', $this->content, PDO::PARAM_STR);
		$Operation->bindValue(':id', $this->id, PDO::PARAM_INT);
		
		if($Operation->execute()) {
			return true;	
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 * Creates a form, compliting it with data of the comment. If the comment has its own id, it puts a path to edit it.
	 * In other case, the path leads to adding a new comment.
	 *
	 * Returns HTML code of the form.
	 *
	**/
	public function getForm() {
		$editing = $this->id;
		
		$path = ($editing) ? $this->getEditPath() : $this->getAddPath();		
		
		$form = "\n<form class=\"rt_form rt_comment_form\" action=\"{$path}\" method=\"post\">";
			$form .= "\n\t<fieldset>";
				$form .= "\n\t\t<input name=\"id\" type=\"hidden\" value=\"{$this->id}\" />";
				$form .= "\n\t\t<input name=\"name\" type=\"hidden\" value=\"{$this->name}\" />";
				$form .= "\n\t\t<input name=\"admin_id\" type=\"hidden\" value=\"{$this->User->getId()}\" />";
				$form .= "\n\t\t<input name=\"login\" type=\"hidden\" value=\"{$this->User->getLogin()}\" />";
				
				$form .= "\n\n\t\t<div class=\"rt_user_informations\">";
					$form .= "\n\t\t\t<p>{$this->getNick()}</p>";
				$form .= "\n\t\t</div>";
				
				$form .= "\n\n\t\t<label class=\"rt_content\">";
					$form .= "\n\t\t\tTreść:";
					$form .= "\n\t\t\t<textarea name=\"content\">{$this->content}</textarea>";
				$form .= "\n\t\t</label>";
				
				$form .= "\n\n\t\t<button type=\"submit\">OK</button>";
				if($editing) $form .= "\n\t\t<a class=\"rt_return\" href=\"".$_SESSION['return']."\">&lt;&lt; Powrót</a>";
			$form .= "\n\t</fieldset>";
		$form .= "\n</form>";
		
		return $form;
	}
}
 
?>