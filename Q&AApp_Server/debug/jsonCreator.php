<?php
include "../../../coreFuncs.php";
include "../../../mysqlHelpers.php";
include "../environmentConstants.php";
include "../constants.php";
include "../qaMysql.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>JSON Debugger</title>
<script>
<?php
	$sessionQuery=mysqli_query($qaCon,"SELECT * FROM session");
	$sessionInfo=mysqli_fetch_array($sessionQuery);
	$sessionId=intval($sessionInfo['id']);
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

	$sessionKey=$sessionId.$sessionInfo['sessionKey'];

	echo 'var session='."'".'"session":"'.$sessionKey.'",'."';";
?>

</script>

<script src="jsonScripts.js"></script>
<script>
function changeInput(msgId)
{
	document.getElementById('jsonId').innerHTML=msgInput[msgId];
}
</script>

</head>

<body>
<form name="testRequest" method="post" enctype="multipart/form-data" action="<?php echo ROOT_URL;?>/request.php">
	<a href="javascript:changeInput(0)">[Null]</a>&nbsp;
	<a href="javascript:changeInput(1)">[Login]</a>
	<a href="javascript:changeInput(2)">[Create Question]</a>
	<a href="javascript:changeInput(3)">[Get Question List]</a>
	<a href="javascript:changeInput(4)">[Get Question Info]</a>
	<a href="javascript:changeInput(5)">[Create Answer]</a>
	<a href="javascript:changeInput(6)">[Get Answer List]</a>
	<a href="javascript:changeInput(7)">[Get Answer Info]</a>
	<a href="javascript:changeInput(8)">[Vote on Answer]</a>
	<a href="javascript:changeInput(9)">[Vote Best Answer]</a><br />
<textarea id="jsonId" name="json" style="width:1000px;height:500px;">
{
	"session":"<?php echo $sessionKey;?>",
	"msgId":"0"
}
</textarea>
<br/>name=file0: <input name="file0" type="file" />
<br/>name=file1: <input name="file1" type="file" />
<br/>name=file2: <input name="file2" type="file" />
<br/>name=file3: <input name="file3" type="file" />
<br/>name=file4: <input name="file4" type="file" />

<br/><input name="Submit1" type="submit" value="submit" />

</form>
</body>

</html>
