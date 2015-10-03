<?php

function msgVoteAns($answerIdIn,$voteIn)
{

	global $qaCon;//contains the mysql connection for the qa app
	global $date;//contains the epoch timestamp for the current time
	global $debug;//determines if debugging mode is on
	global $userId;//determines the current user connected to the session
	global $sessionId;//determines if a session is currently in use

	$answerQuery=NULL;//query to check if answer id is valid
	$ansVoteResults=NULL;//mysql data structure to test if answer id is valid
	$ansVoteId=0;//ID of the vote the user is tring to remove
	
	$ansVotesQuery=NULL;//query used to check if a user has already voted on a question
	$numVoteResults=NULL;//number of results from $ansVotesQuery to check if a user has already voted on an answer

	$output=Array();//the entire message output will be stored here
	$results=Array();//the message results will be stored here
	$voteVal;

	//change format
	$vote=intval($voteIn);
	$answerId=intval($answerIdIn);
	
	///////////////////
	//ERROR CHECKING//
	//////////////////

	//make sure the session is active
	if ($sessionId==0){
		$output[ERROR]=createError(ERR_SESSION,"");	
		return $output;
	}
	
	//check variable formatting
	//answer id is missing
	if ($answerIdIn==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing answer id");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return $output;
	}	
	//missing vote
	if ($voteIn==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing vote");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return $output;
	}
	//bad answer id
	if ($answerId==0){
		if ($debug==1){
			$output[ERROR]=createError(ERR_BADINFO,"Answer id cannot be 0! (The current value is ".$answerId.")");
		}else{
			$output[ERROR]=createError(ERR_BADINFO,"");
		}
		return $output;
	}
	//check vote 
	if ($vote!=1 && $vote!=0){
		if ($debug==1){
			$output[ERROR]=createError(ERR_BADINFO,"Invalid vote valud! (must be 1 or 0 : The current value is vote=".$vote.")");
		}else{
			$output[ERROR]=createError(ERR_BADINFO,"");
		}
		return $output;
	}
	//ensure the userId is not 0 (this means there is a server error)
	if ($userId==0)
	{
		logError(LOG_HIGH,"QA_App","msgVoteAns.php","msgVoteAns()","userId is 0!");
		$output[ERROR]=ERR_SERVER;
		return $output;
	}

	//make sure the answer id is valid
	$answerQuery=mysql_query_log($qaCon,"SELECT id FROM answers WHERE id={$answerId}","QA_App","msgGetAns.php","msgGetAns()");
	$answerInfo=mysqli_fetch_array($answerQuery);
	if (intval($answerInfo['id'])==0)
	{
		if ($debug==1){
			$output[ERROR]=createError(ERR_INVALIDID,"The answerId ".$answerId." is invalid!");
		}else{
			$output[ERROR]=createError(ERR_INVALIDID,"");
		}
		return $output;	
	}




	//check if the user has already voted on the answer
	$ansVotesQuery=mysql_query_log($qaCon,"SELECT id FROM answer_votes WHERE userId={$userId} and answerId={$answerId} ","QA_App","msgVoteAns.php","msgVoteAns()");
	$ansVoteResults=mysqli_fetch_array($ansVotesQuery);

	//the user has not yet voted on the question
	if ($ansVoteResults['id']=='')
	{
		//make sure the user is not trying to remove a non-existent vote
		if ($vote==0){
			if ($debug==1){
				$output[ERROR]=createError(ERR_VOTEBA,"There are no votes to remove from the answer with answerID=".$answerId);
			}else{
				$output[ERROR]=createError(ERR_VOTEBA,"");
			}
			return $output;
		}

		//record vote to later check if the user has voted on the answer
		mysql_query_log($qaCon,"INSERT INTO answer_votes (userId, answerId) VALUES ({$userId},{$answerId}) ","QA_App","msgVoteAns.php","msgVoteAns()");
		//add vote to answer counter
		mysql_query_log($qaCon,"UPDATE answers SET votes=votes+1 WHERE id={$answerId}","QA_App","msgVoteAns.php","msgVoteAns()");
	}
	else
	{
		//make sure the user is not trying to add two votes
		if ($vote==1){
			if ($debug==1){
				$output[ERROR]=createError(ERR_VOTEBA,"The user has already voted on the answer with answerID=".$answerId);
			}else{
				$output[ERROR]=createError(ERR_VOTEBA,"");
			}
			return $output;
		}

		//get the id of the vote we need to remove
		$ansVoteId=intval($ansVoteResults['id']);

		//remove the vote from the list of votes the user has made
		mysql_query_log($qaCon,"DELETE FROM answer_votes WHERE id={$ansVoteId}","QA_App","msgVoteAns.php","msgVoteAns()");
		//add vote to answer counter
		mysql_query_log($qaCon,"UPDATE answers SET votes=votes-1 WHERE id={$answerId}","QA_App","msgVoteAns.php","msgVoteAns()");
	}
	
	//record vote for analysis purposes
	mysql_query_log($qaCon,"INSERT INTO answer_vote_log (userId, answerId,action,date) VALUES ({$userId},{$answerId},{$vote},{$date}) ","QA_App","msgVoteAns.php","msgVoteAns()");
	$results["success"]=1;
	$output[RESULTS]=$results;
	return $output;
}
?>
