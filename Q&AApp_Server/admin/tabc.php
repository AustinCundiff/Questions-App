<?php

include "head.php";
if (login())
{
$table=$_POST['table'];
$do=$_POST['do'];
$user=$_POST['user'];
$pass=$_POST['pass'];

if ($do=='create')
{if (!mysql_query("CREATE TABLE {$table}(id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id))"))
{echo mysql_error();} }

if ($do=='delete')
{if (!mysql_query("DROP TABLE {$table}"))
{echo mysql_error();} }


echo '<form method="POST" action="go.php">';
echo '<input type="hidden" name="user" value="';
echo $user;
echo '">';
echo '<input type="hidden" name="pass" value="';
echo $pass;
echo '">';
echo '<input type="submit" name="f" value="Back"></form>';

}
else
{echo '<meta http-equiv="refresh" content="0; url='.$redirectURL.'">';}
?>