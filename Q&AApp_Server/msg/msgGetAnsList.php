<?php
include_once "helperFunctions/loadAnswers.php";

function msgGetAnsList($questionIdIn,$continueIn)
{

	global $qaCon;//contains the mysql connection for the qa app
	global $date;//contains the epoch timestamp for the current time
	global $debug;//determines if debugging mode is on
	global $sessionId;//determines if a session is currently in use
		
	$output=Array();//the entire message output will be stored here
	$results=Array();//the message results will be stored here
	
	$sessionRequest=NULL;//structure that holds information which can be used to make a continue request
		
	$numResults=0;//the number of questions returned by the MySql query
	$lastPage=0;//whether or not this is the last page/request for questions. (1=it is the last page,0=it is not the last page)
	
	
	//format variables
	$continue=intval($continueIn);	
	$questionId=intval($questionIdIn);	

	
	
	///////////////////
	//ERROR CHECKING//
	//////////////////
	
	//make sure the session is active
	if ($sessionId==0){
		$output[ERROR]=createError(ERR_SESSION,"");
		return $output;
	}
	
	//check variable formatting
	//continue is missing
	if ($continueIn==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing continuePrev variable");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return $output;
	}

	//all required values must be present when continue is 0
	if ($continue==0)
	{
		//questionId is missing
		if ($questionIdIn==""){
			if ($debug==1){
				$output[ERROR]=createError(ERR_MISSINFO,"Missing questionId variable");
			}else{
				$output[ERROR]=createError(ERR_MISSINFO,"");
			}
			return $output;
		}
		if ($questionId<=0)
		{
			if ($debug==1){
				$output[ERROR]=createError(ERR_BADINFO,"questionId must be larger than 0!");
			}else{
				$output[ERROR]=createError(ERR_BADINFO,"");
			}
			return $output;
		}
	} 
	//all values must be empty if continue==1 or continue==2
	else if ($continue==1)
	{
		//questionId is not needed
		if ($questionIdIn!=""){
			if ($debug==1){
				$output[ERROR]=createError(ERR_EXTRAINFO,"questionId not needed if list is being continued.");
			}else{
				$output[ERROR]=createError(ERR_EXTRAINFO,"");
			}
			return $output;
		}
	}
	//continue is in the incorrect format
	else
	{
		if ($debug==1){
			$output[ERROR]=createError(ERR_BADINFO,"continuePrev variable is not 0 or 1");
		}else{
			$output[ERROR]=createError(ERR_BADINFO,"");
		}
		return $output;
	}
	//make sure the question id is valid (only do this if we're not continuing from a past query)
	if ($questionId!=0)
	{
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
	}
	



	
	////////////////////////////////
	//LOADING PREVIOUS SEARCH INFO//
	////////////////////////////////
	
	//get information from the previous search if we're continuing
	if ($continue>0)
	{
		//check if there was a previous session to continue from and get its data ($continue value corresponds to the requestType value)
		$sessionRequest=requestSessionFunctionsPullSessionData($sessionId,3);
		if ($sessionRequest==NULL)
		{
			if ($debug==1){
				$output[ERROR]=createError(ERR_SEARCHCONFLICT,"No previous search found");
			}else{
				$output[ERROR]=createError(ERR_SEARCHCONFLICT,"");
			}
			return $output;
		}
	}
	
	

	//////////////////////////////
	//PERFORM THE ACTUAL SEARCH//
	/////////////////////////////
	$results['answers']=loadAnswersLoad($sessionRequest,$questionId,$lastPage,$numResults);
	$results['numAnswers']=$numResults;
	$results['lastPage']=$lastPage;


	//return the question list
	$output[RESULTS]=$results;
	return $output;
}

?>