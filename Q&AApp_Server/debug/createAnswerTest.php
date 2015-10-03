<?php
include "../constants.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="en-us" http-equiv="Content-Language" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Untitled 1</title>
</head>

<body>

<form method="post" enctype="multipart/form-data"  action="<?php echo ROOT_URL;?>request.php?msgId=5&session=001GBkkE&par0=1&par1=New Ans&par2=desc&par3=1">
	<strong>Title: </strong>New Ans<br />
	<strong>Description: Desc <br />
	File Type: </strong>1 (Picutre)<br />
	<input name="file0" type="file" /><br />
	<input name="Submit1" type="submit" value="submit" /></form>

</body>

</html>
