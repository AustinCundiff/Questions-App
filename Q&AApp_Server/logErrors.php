<?php
define("LOG_EMAIL","walls.trent@switchit001.com");
define("LOG_FILE_LOCATION","log.txt");
define("LOG_LOW",0);
define("LOG_MED",1);
define("LOG_HIGH",2);
define("LOG_SEVERE",3);

$logFile=0;

function openErrorLog()
{
	global $logFile;
	$accessType="None";
	
	if ($logFile==0)
	{
		if (file_exists(LOG_FILE_LOCATION)) 
		{
			$logFile=fopen(LOG_FILE_LOCATION,"a");
			$accessType="Append";
		}
		else
		{
			$logFile=fopen(LOG_FILE_LOCATION,"w");
			$accessType="Write";
		}
	}

	if ($logFile==0)
	{
		mail(LOG_EMAIL,"SEVERE Error- Logging System!","An error occured while trying to save to ".LOG_FILE_LOCATION.". Access type: ".$accessType);
	}
}

function closeErrorLog()
{
	global $logFile;
	if ($logFile==0)
	{
		fclose($logFile);
	}
}


function logError($level,$app,$file,$function,$error)
{
	global $logFile;
	$date=time();
	$errorOut="";

	openErrorLog();
	$errorOut=date("Md,Y H:i:s",$date).": ".$error."(in ".$function." in ".$file." in ".$app.")\n";

	fwrite($logFile,$errorOut,strlen($errorOut));
	ini_set('display_errors',1);

	if ($level==LOG_HIGH)
	{
		mail(LOG_EMAIL,"HIGH Error- ".$app,$errorOut);
	}
	if ($level==LOG_SEVERE)
	{
		mail(LOG_EMAIL,"SEVERE Error- ".$app,$errorOut);
	}
}

?>
