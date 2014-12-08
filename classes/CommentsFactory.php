<?php

/********************************************************************
 *
 *  File's name: CommentsFactory.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 23.11.2014r.
 *  Last modificated: 06.12.2014r.
 *
 ********************************************************************/

// pomysl na odseparowanie obiektu tworzacego encje jest dobry, to faktycznie jest rodzaj fabryki a dokladnie repozytorium
class CommentsFactory {
	protected $query, $ids, $name, $limit, $order;
	
	public function __construct() {
		$ct = COMMENTS_TABLE;		
		$at = ADMINS_TABLE;
		
		$this->query = "SELECT *, {$at}.admin_id, {$at}.login, {$at}.last_visit FROM {$ct} LEFT JOIN {$at} ON {$ct}.admin_id = {$at}.admin_id";	
		$this->order = true;
	}
	
	/**
	 *
	 * @param $ids Array - an array of numbers.
	 *
	 * Sets identifiers of comments, which have to be loaded.
	 *
	**/
	public function setIds($ids) {
		if(!is_array($ids) || !IsIntegerArray($ids)) throw new CommentException('@param $ids musi być tablicą liczb całkowitych!');
		
		$this->ids = $ids;	
	}
	
	/**
	 *
	 * Returns the array with numbers.
	 *
	**/
	public function getIds() {
		return (is_array($this->ids)) ? $this->ids : false;	
	}
	
	/**
	 *
	 * @param $name String - the name of the comments' group
	 *
	 * Sets the name of the group of comments, which they have to be loaded from.
	 *
	**/
	public function setName($name) {
		$this->name = $name;	
	}
	
	/**
	 *
	 * Returns the name of the comments' group.
	 *
	**/
	public function getName() {
		return (!is_null($this->name)) ? $this->name : false;	
	}

	/**
	 *
	 * @param $limit Array(2) - an array, where the first element is the row, after which it has to load data,
	 *                          and the second element is a number of loading rows
	 *
	 * Sets the limit parameters.
	 *
	**/
	public function setLimit($limit) {
		if(!count($limit) != 2 || !IsIntegerArray($limit)) throw new Exception('@param $limit musi być dwuelementową tablicą liczb całkowitych.');
		
		$this->limit = $limit;
	}
	
	/**
	 *
	 * Returns the array with informations about limit of loading comments.
	 *
	**/
	public function getLimit() {
		return (is_array($this->limit)) ? $this->limit : false;
	}
	
	/**
	 *
	 * @param $order Boolean - true -> growing
	 *                         false -> improving
	 *
	 * Sets the type of records' ordering.
	 *
	 * Throws CommentException.
	 *
	**/
	public function setOrder($order) {
		if(!is_bool($order)) throw new CommentException('@param $order musi być wartością logiczną.'); // uzywaj ladniejszego stylu, rzuc exception w nowej lini po {
				
		$this->order = $order;	
	}
	
	/**
	 *
	 * Returns the type of ordering as a string, ready to put into a query.
	 *
	**/
	public function getOrder() {
		return ($this->order) ? "ASC" : "DESC";	
	}
	
	/**
	 *
	 * Loads comments, considering set paremeters.
	 *
	 * Returns CommentsWriter object or false.
	 *
	**/
	public function get() {
		$requirement = '';		
		
		if($name = $this->getName()) {
			$requirement = " WHERE name = :name";
		}
	
		if($ids = $this->getIds()) {
			if(empty($requirement)) $requirement = " WHERE";
			
			$requirement .= " id IN(".implode(', ', $ids).")";	
		}
	
		$this->query .= $requirement." ORDER BY id ".$this->getOrder();	
	
		if($limit = $this->getLimit()) {
			$this->query .= " LIMIT".$limit[0].", ".$limit[1];	
		}	
	
		$PDO = DataBase::getInstance(); // singletony ucza lenistwa i robia syf
		
		$Operation = $PDO->prepare($this->query);
		if($this->getName()) $Operation->bindValue(':name', $this->getName(), PDO::PARAM_STR);
		$Operation->execute();
		
		if($Operation->rowCount() > 0) {
			$comments = array();			
			
			while($comment = $Operation->fetch()) {
				if($comment['admin_id'] !== null) {
					$User = new User($comment['admin_id'], $comment['login'], Date::create($comment['last_visit']));
					$comments[] = new AdminComment($comment['id'], $comment['name'], $User, $comment['content'], Date::create($comment['date']));	
				}
				else {
					$comments[] = new PlainComment($comment['id'], $comment['name'], $comment['nick'], $comment['email'], $comment['www'], $comment['content'], Date::create($comment['date']));
				}
			}
		
			return new CommentsWriter($comments);
		}
		else {
			return false;	
		}
	}
} 
 
?>