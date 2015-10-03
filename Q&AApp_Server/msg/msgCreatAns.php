<?php

include_once "helperFunctions/qaPartsSave.php";

function msgCreatAns($questionIdIn,$title,$partsIn,$file)
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
	$answerId=0;//stores the answer id for image processing
	$thumbnailId=-2;//the part id of the answers's thumbnail (-2=no thumbnail, -1=has a thumbnail & done processing, >-1=part order for thumbnail, wating to be processed)

	//these variables hold part values until they can be processed
	$qaParts=Array();//an array of all the qaParts broken down into the information required to save them
	
	//DEPRECIATED
	$fileSize=0;//will hold the size of the uploaded file	
	$fileId=0;//stores the file id for image porcessing
	//$fileFinal;//the final file path will be temporarily stored here
	//$questionQuery;//query used to check if the question id is valid
	//$questionInfo;//array holding info used to determin if question id is valid
	
	//format variables
	$questionId=intval($questionIdIn);
	
	//make sure the session is active
	if ($sessionId==0){
		$output[ERROR]=createError(ERR_SESSION,"");
		return $output;
	}

	//check variable formatting
	//missing questionId
	if ($questionIdIn==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing question id");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return $output;
	}
	//missing title
	if ($title==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing title");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return $output;
	}
	//bad question id
	if ($questionId==0){
		if ($debug==1){
			$output[ERROR]=createError(ERR_BADINFO,"Question id cannot be 0");
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
	//make sure the question id is valid
	$questionQuery=mysql_query_log($qaCon,"SELECT id FROM questions WHERE id={$questionId}","QA_App","msgGetAns.php","msgGetAns()");
	$questionInfo=mysqli_fetch_array($questionQuery);
	if (intval($questionInfo['id'])==0)
	{
		if ($debug==1)
		{
			$output[ERROR]=createError(ERR_INVALIDID,"Invalid question id! (ID Inputted: ".$questionInfo['id'].")");
		}
		else
		{
			$output[ERROR]=createError(ERR_INVALIDID,"");
		}
		return $output;	
	}

	//verify that all the answer parts that were uploaded are valid
	$tempOutput=qaPartsSaveVerify($partsIn,$file,$thumbnailId,$qaParts);
	if ($tempOutput!=NULL)
	{
		return $tempOutput;
	}
	
	//create the answer in the database
	mysql_query_log($qaCon,"INSERT INTO answers (questionId,title,date,userId,thumbnail) VALUES ({$questionId},'{$title}',{$date},{$userId},{$thumbnailId})","QA_App","msgCreatAns.php","msgCreatAns()");
	$answerId=mysqli_insert_id($qaCon);
	
	//save qaParts
	$tempOutput=qaPartsSaveSave($qaParts,1,$answerId);
	if ($tempOutput!=NULL)
	{	
		//delete the record since a save was not possible
		mysql_query_log($qaCon,"DELETE FROM answers WHERE id={$answerId}","QA_App","msgCreatAns.php","msgCreatAns()");
		return $tmpOutput;	
	}
	
	$results["success"]=1;
	$output[RESULTS]=$results;
	return $output;
}

?>