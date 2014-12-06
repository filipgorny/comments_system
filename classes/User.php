<?php

/********************************************************************
 *
 *  File's name: User.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 10.11.2014r.
 *  Last modificated: 04.12.2014r.
 *
 ********************************************************************/

class User {
	protected $id, $login, $LastVisit;
	
	public function __construct($id, $login, Date $LastVisit = null) {
		$this->id = $id;
		$this->login = $login;
		$this->LastVisit = $LastVisit;
	}

	/**
	 *
	 * Returns user's id.
	 *
	**/
	public function getId() {
		return $this->id;
	}
	
	/**
	 *
	 * Returns user's login.
	 *
	**/
	public function getLogin() {
		return $this->login;
	}
	
	/**
	 *
	 * Returns the Date object with date, when user was in administrative panel recently.
	 *
	**/
	public function getLastVisit() {
		return $this->LastVisit;	
	}
	
	/**
	 *
	 * Updates the date of the last visit in the admin's panel.
	 *
	**/
	public function updateLastVisit() {
		$query = "UPDATE ".ADMINS_TABLE." SET last_visit = :last_visit WHERE admin_id = :admin_id";

		$PDO = DataBase::getInstance();
		$Operation = $PDO->prepare($query);
		$Operation->bindValue(':last_visit', date('Y-m-d H:i:s'), PDO::PARAM_STR);
		$Operation->bindValue(':admin_id', $this->id, PDO::PARAM_INT);
		
		if($Operation->execute()) {
			return true;	
		}
		else {
			return false;	
		}
	}
	
	/**
	 *
	 * Logs the user out.
	 *
	 * Returns true or false.
	 *
	**/
	public function logout() {
		$old = $_SESSION['user'];
		unset($_SESSION['user']);
		session_destroy();	
		
		if(!empty($old) && !isset($_SESSION['user'])) {
			return true;
		}
		else {
			return false;	
		}
	}
	
	/**
	 *
	 * @param $login String
	 *        $password String
	 *
	 * Compares login and password given in arguments with data in the database.
	 *
	 * Returns the User object or false.
	 *
	**/
	static public function login($login, $password) {
		$query = "SELECT * FROM ".ADMINS_TABLE." WHERE login = :login";
		
		$PDO = DataBase::getInstance();
		$Operation = $PDO->prepare($query);
		$Operation->bindValue(':login', $login, PDO::PARAM_STR);
		$Operation->execute();
		
		if($result = $Operation->fetch()) {
			$real_password = $result['password'];
			
			if($real_password === crypt($password, $real_password)) {
				$_SESSION['user'] = array('id' => $result['admin_id'], 'login' => $result['login'], 'last_visit' => $result['last_visit']);								
				
				$LastVisit = Date::create($result['last_visit']);				
				
				return new User($result['admin_id'], $result['login'], $LastVisit);	
			}
			else {
				return false;
			}
		}
		else {
			return false;	
		}
	}
	
	/**
	 *
	 * Returns an User object of admin, who is loged in currently.
	 *
	**/
	static public function getInstance()
	{
		if(isset($_SESSION['user']))
		{
			return new self($_SESSION['user']['id'], $_SESSION['user']['login'], Date::create($_SESSION['user']['last_visit']));
		}
		else
		{
			return false;	
		}	
	}
}
 
?>