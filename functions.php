<?php

/********************************************************************
 *
 *  File's name: functions.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 08.11.2014r.
 *  Last modificated: 06.12.2014r.
 *
 ********************************************************************/

function __autoload($classname) {
	$path = "classes/{$classname}.php";
	$interface_path = "interfaces/{$classname}.php";
	
	if(file_exists($path)) {
		include_once($path);	
	}
	else if(file_exists($interface_path)) {
		include_once($interface_path);	
	}
}

/**
 *
 * @param $email String
 *
 * Checking, if the email is correct.
 *
 * Returns true or false.
 *
**/
function CheckEmail($email) {
	return (preg_match("/^[a-zA-Z]+[a-zA-Z0-9\._\-]+@[a-zA-Z0-9\-]+\.[a-z]+$/", $email) && strlen($email) <= 255) ? true : false;
}

/**
 *
 * @param $www String
 *
 * Checking, if the website address is correct.
 *
 * Returns true or false.
 *
**/
function CheckWebsite($www) {
	return (preg_match("/^(http:\/\/)?(www\.)?[a-zA-Z]+[a-zA-Z0-9\-\.][a-zA-Z]+\.[a-zA-Z]+$/", $www) && strlen($www) <= 255) ? true : false;	
}

/**
 *
 * @param $number Mixed
 *        $numeral_quantity Integer - quantity of digits in number
 *
 * Adds few leading zeros to make that the number will have the same digits quantity like $numeral_quantity.
 *
**/
function AddZeros($number, $numeral_quantity = 2) {
	$number = (int) trim($number);
	
	$length = strlen($number);
						
	for(; $length < $numeral_quantity; $length++) {
		$number = '0'.$number;	
	}		
	
	return $number;
}	

/**
 *
 * Adds 'http://' prefix to the webpage's address, if that doesn't exists.
 *
 * Returns website's address with 'http://' prefix..
 *
**/
function AddHttp($www) {
	$pos = strpos($www, 'http://');

	if($pos !== 0) {
		$www = 'http://'.$www;
	} 
 	
 	return $www;
}

/**
 *
 * @param $string String
 *
 * Deletes the <br /> tags from the string.
 *
 * Returns the string without these tags.
 *
**/
function DeleteBrs($string) {
	return str_replace('<br />', PHP_EOL, $string);	
}

/**
 *
 * @param $array Array
 *
 * Checks, whether the array contains only numerous values.
 *
 * Returns true or false.
 *
**/
function IsIntegerArray($array) {
	if(!is_array($array)) return false;
	
	foreach($array as $element) {
		if(!preg_match('/^[0-9]+$/', $element)) return false;	
	}

	return true;
}

function Correct($content, $link = false) {
	$path = ($link) ? $link : $_SESSION['return'];
	return "<div class=\"rt_correct\">{$content} <a href=\"{$path}\">Powrót</a></div>";
}

function Error($content, $link = false) {
	$path = ($link) ? $link : $_SESSION['return'];
	return "<div class=\"rt_error\">{$content} <a href=\"{$path}\">Powrót</a></div>";
}

function Warning($content) {
	return "<div class=\"rt_warning\">{$content}</div>";	
}

function Comments($name) {
	try {
		$info = "\n\n<p class=\"rt_login_info\">";

		if($U = User::getInstance()) {
			$info .= "Zalogowany jako ".$U->getLogin()." (<a href=\"logout.php\">Wyloguj</a>) | <a href=\"panel.php\">Panel administracyjny</a>";
			$NewComment = new AdminComment(null, $name, $U, null, Date::create(date('Y-m-d H:i:s')));
		}
		else {
			$U = null;
			$info .= "<a href=\"login.php\">Zaloguj</a>";
			$NewComment = new PlainComment(null, $name, null, null, null, null, Date::create(date('Y-m-d H:i:s')));
		}

		$info .= "</p>";

		echo $info;

		echo $NewComment->getForm();

		$flag = (!empty($U)) ? true : false;

		if($Comments = CommentsWriter::createByName($name)) {
			echo $Comments->get($flag);
		}
		else {
			echo Warning("Nikt nie dodał jeszcze komentarza.");	
		}
	}
	catch(CommentException $CE) {
		echo Warning($CE->getMessage());
	}
	catch(Exception $E) {
		echo Warning($E->getMessage());
	}	
}	
?>