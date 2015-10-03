
<?php


include "head.php";
if (login())
{
$table=$_POST['table'];

echo '<form method="POST" action="add.php">';
echo '<table border="1">';
echo '<tr>';
echo '<td>Name</td>';
echo '<td>Type</td>';
echo '<td>Null</td>';
echo '<td>Key</td>';
echo '<td>Defualt</td>';
echo '<td>Extra</td>';
echo '<td>Delete</td>';
echo '</tr>';
$col= array();
$col[0]=0;

$result = mysql_query("SHOW COLUMNS FROM {$table}");
    while ($row = mysql_fetch_assoc($result)) {
     
     echo '<tr>';
     $col[0]+=1;
     $col[$col[0]]="{$row[Field]}";
     echo "<td>{$row[Field]}</td>";
     echo "<td>{$row[Type]}</td>";
     echo "<td>{$row[Null]}</td>";
     echo "<td>{$row[Key]}</td>";
     echo "<td>{$row['Default']}</td>";
     echo "<td>{$row[Extra]}</td>";
     echo "<td><input type='submit' name='Delete_".$row['Field']."' value='Delete'></td>";
     echo '</tr>';
    }



echo '<tr>';
echo '<td><input type="text" name="col" size="10"></td>';
echo '<td><input type="radio" name="type" value="tint">TINYINT(127 signed)<br>';
echo '<input type="radio" name="type" value="sint">SMALLINT(32767 signed)<br>';
echo '<input type="radio" name="type" value="real">INT(2,147,483,648)<br>';
echo '<input type="radio" name="type" value="bint">BIGINT(9,223,372,036,854,775,808)<br>';
echo '<input type="radio" name="type" value="string">TEXT<br>';
echo '<input type="radio" name="type" value="char">VARCHAR Array<br>';
echo '<input type="text" name="stringo" size="10" value="Char Length"></td>';
echo '<td></td><td></td>';
echo '<td><input type="text" name="def" size="10"></td>';
echo '</tr>';
echo '</table>';

echo '<input type="hidden" name="table" value="';
echo $table;
echo '">';
echo '<input type="hidden" name="user" value="';
echo $user;
echo '">';
echo '<input type="hidden" name="pass" value="';
echo $pass;
echo '">';
echo '<input type="submit" name="f" value="Add"></form>';




echo '<table border="1"><tr>';
$colt=0;
while ($colt<$col[0])
{$colt+=1;
echo '<td>';
echo $col[$colt];
echo '</td>';}

echo '<td>Delete</td>';

echo '</tr>';

// Make a MySQL Connection
$result = mysql_query("SELECT * FROM {$table}") or die(mysql_error());
echo '<form method="POST" action="delete.php">';

while ($row = mysql_fetch_array($result))
{echo '<tr>';

$colt=0;
while ($colt<$col[0])
{$colt+=1;
echo '<td>';
if ($col[$colt]=='lastlog' || $col[$colt]=='date')
{echo date('H:i:s-m/d/Y',$row[$col[$colt]]);}
else
{echo "{$row[$col[$colt]]}";}
echo '</td>';}

echo '<td>';
echo '<input type="radio" name="id" value="';
echo $row['id'];
echo '">';
echo '</td>';

echo '</tr>';}
echo '</table>';

echo '<input type="hidden" name="table" value="';
echo $table;
echo '">';
echo '<input type="hidden" name="user" value="';
echo $user;
echo '">';
echo '<input type="hidden" name="pass" value="';
echo $pass;
echo '">';
echo '<input type="submit" name="f" value="Do"></form>';










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
