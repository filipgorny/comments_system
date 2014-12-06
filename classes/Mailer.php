<?php

/********************************************************************
 *
 *  Nazwa pliku: Mailer.php
 *  Autor skryptu: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Utworzony: 11.03.2013r.
 *  Ostatnia modyfikacja: 04.12.2014r.
 *
 ********************************************************************/

class Mailer {
	protected $sender, $subject, $content;
	protected $recipients = array();
	protected $headers = "Content-type: text/plain; charset=utf-8";	
	
	public function __construct($subject, $content) {
		$this->subject = $subject;
		$this->content = $content;
	}
	
	/**
	 *
	 * @param $adresat String - an e-mail address of the recipient
	 *
	 * Adds to the object the next e-mail address, which the message will be sended to.
	 *
	**/
	public function addRecipient($recipient) {
		$this->recipients[] = $recipient;	
	}	
	
	/**
	 *
	 * @param $content String - headers' content
	 *
	 * Changes the content of the headers sending with the e-mail.
	 *
	**/
	public function changeHeaders($headers) {
		$this->headers = $headers;	
	}	
	
	/**
	 *
	 * Sends the e-mail.
	 *
	 * Returns true or false.
	 *
	**/
	public function send() {
		$subject = $this->subject;
		$content = $this->content;
		$headers = $this->headers;
		
		foreach($this->recipients as $recipient)
		{
			if(!mail($recipient, $subject, $content, $headers))
			{
				return false;	
			}
		}
		
		return true;	
	}
}

?>