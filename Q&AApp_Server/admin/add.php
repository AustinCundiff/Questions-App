<?php

include "head.php";
if (login())
{
	$table=$_POST['table'];

if ($_POST['f']!="")
{
	$col=$_POST['col'];
	$type=$_POST['type'];
	$len=$_POST['stringo'];
	$def=$_POST['def'];
	
	
	if ($type=='tint')
	{      $query = ("ALTER TABLE {$table} ADD COLUMN {$col} TINYINT DEFAULT '{$def}'");
	      $result = mysql_query($query) 
	        or die(mysql_error());     } 

	if ($type=='sint')
	{      $query = ("ALTER TABLE {$table} ADD COLUMN {$col} SMALLINT DEFAULT '{$def}'");
	      $result = mysql_query($query) 
	        or die(mysql_error());     } 
	
	if ($type=='real')
	{      $query = ("ALTER TABLE {$table} ADD COLUMN {$col} INT DEFAULT '{$def}'");
	      $result = mysql_query($query) 
	        or die(mysql_error());     } 
	
	if ($type=='bint')
	{      $query = ("ALTER TABLE {$table} ADD COLUMN {$col} BIGINT DEFAULT '{$def}'");
	      $result = mysql_query($query) 
	        or die(mysql_error());     } 
	
	if ($type=='string')
	{      $query = ("ALTER TABLE {$table} ADD COLUMN {$col} TEXT");
	      $result = mysql_query($query) 
	        or die(mysql_error());     } 
	
	if ($type=='char')
	{      $query = ("ALTER TABLE {$table} ADD COLUMN {$col} VARCHAR({$len}) DEFAULT '{$def}'");
	      $result = mysql_query($query) 
	        or die(mysql_error());     } 
}
else
{
	$result = mysql_query("SHOW COLUMNS FROM {$table}");
    while ($row = mysql_fetch_assoc($result)) 
	{	
		if ($_POST['Delete_'.$row['Field']]!="")
		{
			$name=$row['Field'];
			echo "b4";
			mysql_query("ALTER TABLE {$table} DROP COLUMN {$name}") or die(mysql_error()); 
		}
	}
}


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