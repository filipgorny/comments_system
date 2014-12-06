<?php

/********************************************************************
 *
 *  File's name: PlainComment.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 08.11.2014r.
 *  Last modificated: 06.12.2014r.
 *
 ********************************************************************/

class PlainComment extends Comment {
	protected $email, $www, $ip;

	public function __construct($id, $name, $nick, $email, $www, $content, Date $Date) {
		parent::__construct($id, $name, $content, $Date);		
		
		$this->nick =  $nick;
		$this->email = $email;
		$this->www =   $www;
		$this->ip =    $_SERVER['REMOTE_ADDR'];
	}

	public function __toString() {
		$comment = "\n\t\t<div class=\"rt_comment\">";
		
			if($this->isAdminLoged()) {
			$comment .= "\n\t\t\t<ul class=\"rt_admin_actions\">";
				$comment .= "\n\t\t\t\t<li><a href=\"".$this->getEditPath()."\">[Edytuj]</a>";	
				$comment .= "\n\t\t\t\t<li><a href=\"".$this->getDeletePath()."\">[Usuń]</a>";	
			$comment .= "\n\t\t\t</ul>";
			}
		
			$comment .= "\n\t\t\t<div class=\"rt_data\">";
				$comment .= "\n\t\t\t\t<p class=\"rt_nick\">{$this->getNick()}</p>";
				$comment .= "\n\t\t\t\t<p class=\"rt_date\">{$this->getDate()->getDate()}</p>";
			
				$comment .= "\n\n\t\t\t\t<ul>";
					if($this->isAdminLoged() && $email = $this->getEmail()) $comment .= "\n\t\t\t\t\t<li><a href=\"mailto:{$email}\">E-mail</a></li>";
					if($www = $this->getWww()) $comment .= "\n\t\t\t\t\t<li><a href=\"{$www}\">Strona www</a></li>";
				$comment .= "\n\t\t\t\t</ul>";			
			$comment .= "\n\t\t\t</div>";
		
			$comment .= "\n\n\t\t\t<p class=\"rt_content\">";
				$comment .= "\n\t\t\t{$this->getContent()}";
			$comment .= "\n\t\t\t</p>";
		$comment .= "\n\t\t</div>";
		
		return $comment;
	}

	/**
	 *
	 * Returns an user's e-mail address or false.
	 *
	**/
	public function getEmail() {
		return (!empty($this->email)) ? $this->email : false;	
	}

	/**
	 *
	 * Returns an user's website or false.
	 *
	**/
	public function getWww() {
		return (!empty($this->www)) ? $this->www : false;
	}
	
	/**
	 *
	 * Returns the id in database and the Date object with time, when a comment was added to this 'name' using this ip address, or false, if it hasn't ever happened.
	 *
	**/
	public function checkIp() {
		$PDO = DataBase::getInstance();
		
		$query = "SELECT * FROM ".IP_TABLE." WHERE name = :name AND ip_address = :ip";
		
		$Operation = $PDO->prepare($query);
		$Operation->bindValue(':name', $this->name, PDO::PARAM_STR);
		$Operation->bindValue(':ip', $this->ip, PDO::PARAM_STR);
		$Operation->execute();
		
		if($Operation->rowCount() > 0) {
			$result = $Operation->fetch();
			
			return array($result['ip_id'], Date::create($result['date']));			
		}
		else {
			return false;	
		}
	}	
	
	/**
	 *
	 * Adds this comment to the database. It bears in mind, when a comment was added using this IP recently.
	 *
	 * Throws Exception.
	 *
	 * Returns true or false.
	 *
	**/
	public function add() {
		$current_time = time();
		$date = date('Y-m-d H:i:s', $current_time);	
		
		$PDO = DataBase::getInstance();				

		if($last = $this->checkIp()) {
			$ip_id = $last[0];
			$LastDate = $last[1];		
			$limit = $LastDate->getUnixTimestamp() + TIME_LIMIT;			
			
			if($current_time < $limit) {
				$differential = $limit - $current_time;
				$to_wait = ceil($differential / 60);				
				
				throw new CommentException("Proszę poczekać jeszcze ".$to_wait." minut(y), aby dodać kolejny komentarz.");		
			}
			else {
				$ip_query = "UPDATE ".IP_TABLE." SET date = :date WHERE ip_id = :id";
				
				$IP = $PDO->prepare($ip_query);
				$IP->bindValue(':date', $date, PDO::PARAM_STR);
				$IP->bindValue(':id', $ip_id, PDO::PARAM_INT);	
			}
		}
		else {
			$ip_query = "INSERT INTO ".IP_TABLE." VALUES(null, :name, :ip_address, :date)";
			
			$IP = $PDO->prepare($ip_query);
			$IP->bindValue(':name', $this->name, PDO::PARAM_STR);
			$IP->bindValue(':ip_address', $this->ip, PDO::PARAM_STR);
			$IP->bindValue(':date', $date, PDO::PARAM_STR);
		}
	
		if(!$IP->execute()) {
			return false;
		}	

		$query = "INSERT INTO ".COMMENTS_TABLE." VALUES(null, :name, :nick, :email, :www, :content, :date, null)";		
		
		$Operation = $PDO->prepare($query);
		$Operation->bindValue(':nick', $this->nick, PDO::PARAM_STR);
		$Operation->bindValue(':name', $this->name, PDO::PARAM_STR);
		$Operation->bindValue(':email', $this->email, PDO::PARAM_STR);
		$Operation->bindValue(':www', $this->www, PDO::PARAM_STR);
		$Operation->bindValue(':content', $this->content, PDO::PARAM_STR);
		$Operation->bindValue(':date', $date, PDO::PARAM_STR);
		
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
		$PDO = DataBase::getInstance();
		
		$query = "UPDATE ".COMMENTS_TABLE." SET nick = :nick, email = :email, www = :www, content = :content WHERE id = :id";
		
		$Operation = $PDO->prepare($query);
		$Operation->bindValue(':nick', $this->nick, PDO::PARAM_STR);
		$Operation->bindValue(':email', $this->email, PDO::PARAM_STR);
		$Operation->bindValue(':www', $this->www, PDO::PARAM_STR);
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
				
		$Captcha = new MathCaptcha();		
		
		$form = "\n<form class=\"rt_form rt_comment_form\" action=\"{$path}\" method=\"post\">";
			$form .= "\n\t<fieldset>";
				$form .= "\n\t\t<input name=\"name\" type=\"hidden\" value=\"{$this->name}\" />";
				if($editing) $form .= "\n\t\t<input name=\"id\" type=\"hidden\" value=\"{$this->id}\" />";
				$form .= "\n\n\t\t<div class=\"rt_user_informations\">";

					$form .= "\n\t\t\t<label>";
						$form .= "\n\t\t\t\tNick:<span>*</span>";
						$form .= "\n\t\t\t\t<input name=\"nick\" type=\"text\" value=\"{$this->nick}\" />";
					$form .= "\n\t\t\t</label>";
					
					$form .= "\n\t\t\t<label>";
						$form .= "\n\t\t\t\tAdres e-mail:";
						$form .= "\n\t\t\t\t<input name=\"email\" type=\"text\" value=\"{$this->email}\" />";
					$form .= "\n\t\t\t</label>";
					
					$form .= "\n\t\t\t<label>";
						$form .= "\n\t\t\t\tStrona internetowa:";
						$form .= "\n\t\t\t\t<input name=\"www\" type=\"text\" value=\"{$this->www}\" />";
					$form .= "\n\t\t\t</label>";
					
					if(!$editing) {
						$form .= "\n\t\t\t<label class=\"rt_captcha\">";
							$form .= "\n\t\t\t\tWykonaj działanie:<span>*</span>";
							$form .= "\n\t\t\t\t<div>{$Captcha->getOperation()}</div><input name=\"user_result\" type=\"text\" />";
							$form .= "\n\t\t\t\t<input name=\"result\" type=\"hidden\" value=\"{$Captcha->getResult()}\" />";
						$form .= "\n\t\t\t</label>";
					}			
				$form .= "\n\t\t</div>";
				
				$form .= "\n\n\t\t<label class=\"rt_content\">";
					$form .= "\n\t\t\tTreść:<span>*</span>";
					$form .= "\n\t\t\t<textarea name=\"content\">".DeleteBrs($this->content)."</textarea>";
				$form .= "\n\t\t</label>";
				
				$form .= "\n\n\t\t<button type=\"submit\">OK</button>";
				if($editing) $form .= "\n\n\t\t<a class=\"rt_return\" href=\"".$_SESSION['return']."\">&lt;&lt; Powrót</a>";	
			$form .= "\n\t</fieldset>";
		$form .= "\n</form>";
		
		return $form;
	}
}
	
?>