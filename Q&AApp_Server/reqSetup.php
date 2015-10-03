<?php

/**
	This function takes in an array of values and prints them out with the correct formatting.

	$output- This is an array of values that should be outputted. It must have at least two values 
		and end with "\0"
*/
function printOutput($output)
{
	echo "***";
	print_r($output);
	$i=0;
	$continue=true;
	while ($continue)
	{
		if ($output[$i]!=END_MSG)
		{
			echo $output[$i].":";
		}
		else
		{
			echo $output[$i];
			$continue=false;
		}
		
		//make sure we're not stuck in an infinite look, this can happen if the END_MSG is not added to the end of the output array
		if ($i>MAX_RET)
		{
			logError(LOG_HIGH,"QA_App","request.php","printOutput()","Infinite loop!");
			break;
		}
		
		//increment for next loop
		$i++;
	}
}









/**
	This function loads information for an inputted session and saves that info in the form of global variables.
	If the session key entered is invalid or expired the function will save a 0 to the sessionId variable and
	will not modify any other variables. (Modified $debug, $userId, $sessionID) Also not that this function requires
	an active mysql connection on $qaCon.
	
	$session- The app session key to load info for
*/
function loadSession($session)
{
	global $qaCon;//the mysql connection to use
	global $debug;//indicates whether or not debugging is on, the function will save a value here
	global $userId;//indicates the current user's id, the function will save a value here
	global $sessionId;//indicates the current sesion id, the function will save a value here

	//make sure the app session key is the correct length
	if (strlen($session)!=8)
	{
		$sessionId=0;
		return;
	}
	
	//pull out the session id & server session key & make sure the id is numeic
	$sessionId=intval(substr($session,0,3));
	$sessionKey=substr($session,3,5);

	if ($sessionId==0)
	{
		return;
	}
	//get session info
	$sessionInfoQuery=mysql_query_log($qaCon,"SELECT id,userId,debug FROM session WHERE id={$sessionId} and sessionKey='{$sessionKey}'","QA_App","request.php","loadSession()");
	$sessionInfo=mysqli_fetch_array($sessionInfoQuery);
    if ($sessionInfo[0]==0)
	{
		$sessionId=0;
		return;
	}

	//save values from the session
	$userId=intval($sessionInfo[1]);
	$debug=intval($sessionInfo[2]);

}





/**
	This is a shortened version of the contodat databse cleaner code.
	This should be used when the contodat function decreases code readability
	due to its long name.
*/
function c($string)
{
	return contodat($string);
}





/**
	This function creates an array containing an error
	id and message 
	
	$id= The error message id
	$message= A custom message with more info about the error
	
	returns an array with the error message in it
*/
function createError($id,$message)
{
	$error=Array();
	$error[ERROR_ID]=$id;
	if ($message!="")
	{
		$error[ERROR_MSG]=$message;
	}
	return $error;
}



//fetch the json parameters
$inJson=$_POST['json'];
$in=json_decode($inJson,true);

//pull common variales from the json array
$session=contodat($in['session']);//the session key
$msgId=intval($in['msgId']);//the message id

//these values are session based
$sessionId=0;//current session id
$debug=0;//debugging mode for the the session
$userId=0;//user attached to the session
$maxReturn=MAX_RET;//the maximum number of arguments that can be returned. For most requests this will be the default MAX_RET constant.

//this will be used to temporarily store output information
$output=Array();




?>