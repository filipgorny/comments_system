<?php

/********************************************************************
 *
 *  File's name: config.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 08.11.2014r.
 *  Last modificated: 27.11.2014r.
 *
 ********************************************************************/

// a database's host name
define('DB_HOST', 'localhost');

// a database's name
define('DB_NAME', 'comments');

// a database's user
define('DB_USERNAME', 'root');

// a password of database's user
define('DB_PASSWORD', '');

// a name of the table cointaining comments
define('COMMENTS_TABLE', 'comments');

// a name of the table containing ip addresses
define('IP_TABLE', 'ip_addresses');

// a name of the table containing admins
define('ADMINS_TABLE', 'admins');

// a name of the file, where comments are adding to the database
define('ADD_FILE', 'add.php');

// a name of the file, where admin can manage comments
define('PANEL_FILE', 'panel.php');

// a name of the file, when admin can log himself in
define('LOGIN_FILE', 'login.php');

// a time limit between adding comments using the same ip address in the same place (seconds)
define('TIME_LIMIT', 60);

$Current = Link::create();

if(isset($_SESSION['last']) && $_SESSION['last'] != $Current->get()) {
	$_SESSION['return'] = $_SESSION['last'];
}
else {
	$_SESSION['return'] = '/';	
}

$_SESSION['last'] = $Current->get();

$admin_emails = array('filip.markiewicz96@gmail.com');

if(isset($_SESSION['user'])) {
	$U = User::getInstance();
}

?>