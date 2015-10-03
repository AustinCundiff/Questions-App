<?php
include_once "helperFunctions/loadAnswers.php";
include_once "helperFunctions/qaPartsLoad.php";

function msgGetQues($questionIdIn,$listParts,$listAnswers)
{

	global $qaCon;//contains the mysql connection for the qa app
	global $date;//contains the epoch timestamp for the current time
	global $debug;//determines if debugging mode is on
	global $sessionId;//determines if a session is currently in use
		
	$questionQuery;//holds the mysql results for the question
	$questionInfo;//holds all the information for the question in an array
		
	$numResults=0;//the number of questions returned by the MySql query
	$lastPage=0;//whether or not this is the last page/request for questions. (1=it is the last page,0=it is not the last page)
		
	$output="";//holds all the ouput for the request
	$results="";//holds the results for the question

			
	
	//format variables
	$questionId=intval($questionIdIn);
	$listParts=intval($listParts);
	$listAnswers=intval($listAnswers);
	
	
	///////////////////
	//ERROR CHECKING//
	//////////////////
	
	//make sure the session is active
	if ($sessionId==0){
		$output[ERROR]=createError(ERR_SESSION,"");
		return $output;
	}
	
	//check variable formatting
	//question id is missing
	if ($questionIdIn==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing questionId");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return $output;
	}
	//mode is in the incorrect format
	if ($listParts!=0 && $listParts!=1){
		if ($debug==1){
			$output[ERROR]=createError(ERR_BADINFO,"listParts variable is not 1 or 0");
		}else{
			$output[ERROR]=createError(ERR_BADINFO,"");
		}
		return $output;
	}
	if ($listAnswers!=0 && $listAnswers!=1){
		if ($debug==1){
			$output[ERROR]=createError(ERR_BADINFO,"listAnswers variable is not 1 or 0");
		}else{
			$output[ERROR]=createError(ERR_BADINFO,"");
		}
		return $output;
	}
	


	//load the question results
	$questionQuery=mysql_query_log($qaCon,"SELECT id,title,category,answers FROM questions WHERE id={$questionId}","QA_App","msgGetQues.php","msgGetQues()");
	$questionInfo=mysqli_fetch_array($questionQuery);
	
	//check if results were returned
	if (intval($questionInfo['id'])==0)
	{
		if ($debug==1){
			$output[ERROR]=createError(ERR_INVALIDID,"The questionId ".$questionId." is invalid!");
		}else{
			$output[ERROR]=createError(ERR_INVALIDID,"");
		}
		return $output;	
	}


	//parse the results
	$results['title']=$questionInfo['title'];
	$results['category']=intval($questionInfo['category']);
	$results['numAnswers']=$questionInfo['answers'];
	

	
	
	
	//load all the parts
	if ($listParts)
	{
		$results['qaParts']=qaPartsLoadLoad(0,$questionId);
	}
	
		
	//load all answers if using that option
	if ($listAnswers)
	{
		$results['answerInfo']=Array();
		$results['answerInfo']['answers']=loadAnswersLoad(NULL,$questionId,$lastPage,$numResults);
		$results['answerInfo']['numAnswers']=$numResults;
		$results['answerInfo']['lastPage']=$lastPage;
	}

		
	//return the question list
	$output[RESULTS]=$results;
	return $output;
}

?>