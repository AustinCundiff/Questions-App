<?php
include_once "helperFunctions/qaPartsLoad.php";

function msgGetAns($answerIdIn,$listParts)
{
	global $qaCon;//contains the mysql connection for the qa app
	global $date;//contains the epoch timestamp for the current time
	global $debug;//determines if debugging mode is on
	global $sessionId;//determines if a session is currently in use
		
	$answerQuery;//holds the mysql results for the question
	$answerInfo;//holds all the information for the question in an array
		
	$output="";//holds all the ouput for the request
	$results="";//holds the results for the question

			
	
	//format variables
	$answerId=intval($answerIdIn);
	$listParts=intval($listParts);
	
	
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
	if ($answerIdIn==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing answerId");
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


	//load the question results
	$answerQuery=mysql_query_log($qaCon,"SELECT id,questionId,title FROM answers WHERE id={$answerId}","QA_App","msgGetAns.php","msgGetAns()");
	$answerInfo=mysqli_fetch_array($answerQuery);

	//check if results were returned
	if (intval($answerInfo['id'])==0)
	{
		if ($debug==1){
			$output[ERROR]=createError(ERR_INVALIDID,"The answerId ".$answerId." is invalid!");
		}else{
			$output[ERROR]=createError(ERR_INVALIDID,"");
		}
		return $output;	
	}


	//parse the results
	$results['questionId']=intval($answerInfo['questionId']);
	$results['title']=$answerInfo['title'];
	

	
	
	
	//load all the parts
	if ($listParts)
	{
		$results['qaParts']=qaPartsLoadLoad(1,$answerId);
	}
	
	
	
	
	//return the question list
	$output[RESULTS]=$results;
	return $output;
}

?>