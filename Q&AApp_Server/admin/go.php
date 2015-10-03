<?php
include "head.php";
if (login())
{
$result = mysql_query('SHOW TABLES FROM '.$database);

echo '<form method="POST" action="table.php">';
echo '<input type="hidden" name="user" value="';
echo $user;
echo '">';
echo '<input type="hidden" name="pass" value="';
echo $pass;
echo '">';

while ($row = mysql_fetch_row($result)) 
{echo '<input type="radio" value="';
echo "{$row[0]}";
echo '" name="table">';
echo "{$row[0]}<br>";}

echo '<input type="submit" value="Go" name="B1">';
echo '</form>';





echo '<br><br><br><br><br>';

echo '<form method="POST" action="tabc.php">';
echo '<input type="hidden" name="user" value="';
echo $user;
echo '">';
echo '<input type="hidden" name="pass" value="';
echo $pass;
echo '">';

echo 'Table <input type="text" name="table" size="20"><br>';
echo '<input type="radio" name="do" value="create">Create<br>';
echo '<input type="radio" name="do" value="delete">Delete<br>';
echo '<input type="submit" name="f"></form>';



}
else
{echo '<meta http-equiv="refresh" content="0; url='.$redirectURL.'">';}
?>
