<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<script src="/js/html5shiv.js"></script>
	<link rel="stylesheet" href="style.css" />
	<title>System komentarzy</title>
</head>
<body>

<?php

include('functions.php');
include('config.php');

$somestring = bin2hex(openssl_random_pseudo_bytes(16));

$salt = '$6$rounds=5000$'.$somestring;
$password = crypt('password', $salt);
echo $password;
//echo "<br />".crypt('password', $password);

Comments('tu');

Comments('owdzie');

?>


</body>
</html>