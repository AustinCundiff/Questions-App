<?php

include "head.php";
if (login())
{
$table=$_POST['table'];
$id=$_POST['id'];


mysql_query("DELETE FROM {$table} WHERE id='{$id}'");






echo '<form method="POST" action="table.php">';
echo '<input type="hidden" name="user" value="';
echo $user;
echo '">';
echo '<input type="hidden" name="pass" value="';
echo $pass;
echo '">';
echo '<input type="hidden" name="table" value="';
echo $table;
echo '">';
echo '<input type="submit" name="f" value="Back"></form>';
}
else
{echo '<meta http-equiv="refresh" content="0; url='.$redirectURL.'">';}
?>