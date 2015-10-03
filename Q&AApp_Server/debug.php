
<head>
<style type="text/css">
td {
	border: 1px solid #000000;
}
</style>
</head>

<?php

include "../../coreFuncs.php";
include "../../mysqlHelpers.php";
include "constants.php";
include "qaMysql.php";



?>

<h1><a href="debug/jsonCreator.php">JSON Tester</a></h1>

<h1>Active Sessions</h1>
<table cellpadding="0" cellspacing="0" style="width: 100%">
	<tr>
		<td>id</td>
		<td>App Session Key</td>
		<td>Server Session Key</td>
		<td>User ID</td>
		<td>Debug On?</td>
	</tr>
	<tr>
	<?php
	$rows=mysqli_query($qaCon,"SELECT * FROM session");
	while ($row=mysqli_fetch_array($rows))
	{
		$sessionId=intval($row['id']);
		//format session id
		if ($sessionId<100)
		{
			if ($sessionId>9)
			{
				$sessionId="0".$sessionId;
			}
			else
			{
				$sessionId="00".$sessionId;
			}
		}

		echo "<tr>";
			echo "<td>".$row['id']."</td>";
			echo "<td>".$sessionId.$row['sessionKey']."</td>";
			echo "<td>".$row['sessionKey']."</td>";
			echo "<td>".$row['userId']."</td>";
			echo "<td>".$row['debug']."</td>";
		echo "</tr>";
	}
	?>
</table>

<h1>Questions</h1>
<p><a href="debug/createQuestionTest.html">Add a question</a></p>
<table cellpadding="0" cellspacing="0" style="width: 100%">
	<tr>
		<td>id</td>
		<td>Date Posted</td>
		<td>Title</td>
		<td>Category</td>
		<td>File Type</td>
		<td>Image Location</td>
	</tr>
	<tr>
	<?php
	$rows=mysqli_query($qaCon,"SELECT * FROM questions");
	while ($row=mysqli_fetch_array($rows))
	{
		$id=intval($row['id']);
		$qDate=intval($row['date']);
		
		$files=mysqli_query($qaCon,"SELECT * FROM files WHERE qaType=0 and qaId={$id}");
		$file=mysqli_fetch_array($files);
		
		$type=intval($file['fileType']);
		if ($type==1)
		{
			$end=IMG_END;
		}
		else
		{
			$end=VID_END;
		}
		echo "<tr>";
			echo "<td>".$id."</td>";
			echo "<td>".date('M d, Y g:i A',$qDate)."</td>";
			echo "<td>".dattocon($row['title'])."</td>";
			echo "<td>".$row['category']."</td>";
			echo "<td>".$type."</td>";
			echo "<td><a target='_blank' href='".ROOT_URL.FILE_DIR.$file['id'].$end."'>".ROOT_URL.FILE_DIR.$file['id'].$end."</a></td>";
		echo "</tr>";
	}
	?>
</table>





<h1>Answers</h1>
<p><a href="debug/createAnswerTest.php">Add a question</a></p>
<table cellpadding="0" cellspacing="0" style="width: 100%">
	<tr>
		<td>id</td>
		<td>Date Posted</td>
		<td>Title</td>
		<td>File Type</td>
		<td>Image Location</td>
	</tr>
	<tr>
	<?php
	$rows=mysqli_query($qaCon,"SELECT * FROM answers");
	while ($row=mysqli_fetch_array($rows))
	{
		$id=intval($row['id']);
	
		$files=mysqli_query($qaCon,"SELECT * FROM files WHERE qaType=1 and qaId={$id}");
		$file=mysqli_fetch_array($files);
		
		$type=intval($file['fileType']);
		if ($type==1)
		{
			$end=IMG_END;
		}
		else
		{
			$end=VID_END;
		}
		echo "<tr>";
			echo "<td>".$id."</td>";
			echo "<td>".date('M d, Y g:i A')."</td>";
			echo "<td>".dattocon($row['title'])."</td>";
			echo "<td>".$type."</td>";
			echo "<td><a target='_blank' href='".ROOT_URL.FILE_DIR.$file['id'].$end."'>".ROOT_URL.FILE_DIR.$file['id'].$end."</a></td>";
		echo "</tr>";
	}
	?>
</table>
