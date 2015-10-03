<?php
$redirectURL="http://dev3.answermeapp.com/admin/";
$user=$_POST['user'];
$pass=$_POST['pass'];
$database="qa_app_dev3";

function login()
{
	?>
	<head>
	<meta http-equiv="Content-Language" content="en-us">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Switchit001.com</title>
	<meta name="Microsoft Theme" content="none">
	</head>
	
	<?php
	global $user;
	global $pass;
	global $database;
	if ($user=='QAdmin' && $pass=='L!48392')
	{
		$con=mysql_connect("localhost", "QA_APP", "AQ56759*") or die(mysql_error());
		mysql_select_db($database) or die(mysql_error());
		return true;
	}
	else
	{
		return false;
	}
}
?>