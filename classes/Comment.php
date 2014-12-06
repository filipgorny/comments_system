<?php

/********************************************************************
 *
 *  File's name: Comment.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 09.11.2014r.
 *  Last modificated: 26.11.2014r.
 *
 ********************************************************************/

abstract class Comment implements CommentInterface {
	protected $id, $name, $nick, $content, $date, $admins_flag;		
	
	public function __construct($id, $name, $content, Date $Date) {
		if(!is_numeric($id) && !empty($id)) throw new CommentException('@param $id musi być liczbą, jeśli nie jest pusty.');		
		if(!preg_match('/^[a-zA-Z]+[a-zA-Z0-9_\-]*$/', $name)) throw new CommentException('@param $name może zawierać tylko znaki alfabetu łacińskiego a-z &ndash; i od nich musi się zaczynać &ndash; liczby 0-9, podkreślenie _ i myślnik -.');
		
		$this->id =          $id;
		$this->name =        $name;
		$this->content =     $content;
		$this->Date =        $Date;
		$this->admins_flag = false;
	}	
	
	abstract public function add();	
	
	/**
	 *
	 * Returns an id of the comment.
	 *
	**/
	public function getId() {
		return $this->id;
	}

	/**
	 *
	 * Returns the name, which the comment is assigned to.
	 *
	**/
	public function getName() {
		return $this->name;
	}

	/**
	 *
	 * Returns an user's nickname.
	 *
	**/
	public function getNick() {
		return $this->nick;
	}

	/**
	 *
	 * Sets a value of the nick.
	 *
	**/
	public function setNick($nick) {
		$this->nick = $nick;	
	}

	/**
	 *
	 * Returns a content of the comment.
	 *
	**/
	public function getContent() {
		return $this->content;	
	}
	
	/**
	 *
	 * Sets a value of the content.
	 *
	**/
	public function setContent($content) {
		$this->content = $content;	
	}	
	
	/**
	 *
	 * Returns a date of the comment.
	 *
	**/
	public function getDate() {
		return $this->Date;	
	}

	/**
	 *
	 * @param $flag Boolean
	 *
	 * Sets a flag, which represents, whether the admin is loged in.
	 *
	**/
	public function setAdminsFlag($flag) {
		if(!is_bool($flag)) throw new CommentException('@param $flag musi być wartością logiczną.');
		
		$this->admins_flag = $flag;	
	}
	
	/**
	 *
	 * Returns the boolean value, which says, whether the admin is loged in.
	 *
	**/
	public function isAdminLoged() {
		return $this->admins_flag;	
	}

	/**
	 *
	 * Returns a path to the file, where a comment will be added.
	 *
	**/
	public function getAddPath() {
		return ADD_FILE;	
	}
	
	/**
	 *
	 * Returns a path to the file, where a comment will be modified.
	 *
	**/
	public function getEditPath() {
		return PANEL_FILE."?action=edit&amp;id=".$this->id;
	}
	
	/**
	 *
	 * Returns a path to the file, where a comment will be deleted.
	 *
	**/
	public function getDeletePath() {
		return PANEL_FILE."?action=delete&amp;id=".$this->id;
	}

	/**
	 *
	 * Deletes the comment.
	 *
	 * Returns true or false.
	 *
	**/
	public function delete() {
		$PDO = DataBase::getInstance();
		
		$query = "DELETE FROM ".COMMENTS_TABLE." WHERE id = :id";
		
		$Operation = $PDO->prepare($query);
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
	 * @param $id Integer - an id of comment, which is wanted to create
	 *
	 * Takes data from the database and creates a self-object, using the id given as a parameter.
	 *
	 * Returns the Comment object or false.
	 *
	**/
	static public function create($id) {
		$ct = COMMENTS_TABLE;
		$at = ADMINS_TABLE;
		
		$query = "SELECT {$ct}.*, {$at}.admin_id, {$at}.login FROM {$ct} LEFT JOIN {$at} ON {$ct}.admin_id = {$at}.admin_id WHERE id = :id";
		
		$PDO = DataBase::getInstance();
		
		$Operation = $PDO->prepare($query);
		$Operation->bindValue(':id', $id, PDO::PARAM_INT);
		$Operation->execute();
		
		if($result = $Operation->fetch()) {
			if(!empty($result['login'])) {
				$U = new User($result['admin_id'], $result['login']);
				
				return new AdminComment($result['id'], $result['name'], $U, $result['content'], Date::create($result['date']));
			}
			else {
				return new PlainComment($result['id'], $result['name'], $result['nick'], $result['email'], $result['www'], $result['content'], Date::create($result['date']));
			}		
		}
		else {
			return false;
		}
	}
}
 
?>