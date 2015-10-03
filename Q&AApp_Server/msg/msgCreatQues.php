<?php
include_once "helperFunctions/qaPartsSave.php";

function msgCreatQues($title,$categoryIn,$partsIn,$file)
{
	global $qaCon;//contains the mysql connection for the qa app
	global $date;//contains the epoch timestamp for the current time
	global $debug;//determines if debugging mode is on
	global $userId;//determines the current user connected to the session
	global $sessionId;//determines if a session is currently in use
	$output=Array();//the entire message output will be stored here
	$tempOutput=NULL;//temporarily holds output structures returned by other function so we can check if they're null without overwritting other data
	$results=Array();//the message results will be stored here
	$questionId=0;//stores the question id for image processing
	$thumbnailId=-2;//the part id of the question's thumbnail (-2=no thumbnail, -1=has a thumbnail & done processing, >-1=part order for thumbnail, wating to be processed)

	//these variables hold part values until they can be processed
	$qaParts=Array();//an array of all the qaParts broken down into the information required to save them
	
	//format variables
	$category=intval($categoryIn);
	
	//make sure the session is active
	if ($sessionId==0){
		$output[ERROR]=createError(ERR_SESSION,"");
		return $output;
	}

	//check variable formatting for main section of question (parts will not be checked yet)
	//missing title
	if ($title==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing title");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return $output;
	}
	//missing category
	if ($categoryIn==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing category");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return $output;
	}
	//bad category
	if ($category==0){
		if ($debug==1){
			$output[ERROR]=createError(ERR_BADINFO,"Category cannot be 0");
		}else{
			$output[ERROR]=createError(ERR_BADINFO,"");
		}
		return $output;
	}
	//ensure the userId is not 0 (this means there is a server error)
	if ($userId==0)
	{
		logError(LOG_HIGH,"QA_App","msgCreatQues.php","msgCreatQues()","userId is 0!");
		if ($debug==1)
		{
			$output[ERROR]=createError(ERR_SERVER,"Server Error!");
		}
		else
		{
			$output[ERROR]=createError(ERR_SERVER,"");
		}
		return $output;
	}


	//check qaParts
	$tempOutput=qaPartsSaveVerify($partsIn,$file,$thumbnailId,$qaParts);
	if ($tempOutput!=NULL)
	{
		return $tempOutput;
	}

	//create the question in the database
	mysql_query_log($qaCon,"INSERT INTO questions (userId,title,category,date,thumbnail) VALUES ({$userId},'{$title}',{$category},{$date},{$thumbnailId})","QA_App","msgCreatQues.php","msgCreatQues()");
	$questionId=mysqli_insert_id($qaCon);
	
	//save qaParts
	$tempOutput=qaPartsSaveSave($qaParts,0,$questionId);
	if ($tempOutput!=NULL)
	{	
		//delete the record since a save was not possible
		mysql_query_log($qaCon,"DELETE FROM questions WHERE id={$questionId}","QA_App","msgCreatQues.php","msgCreatQues()");
		return $tmpOutput;	
	}
	
	$results["success"]=1;
	$output[RESULTS]=$results;
	return $output;
}

?>