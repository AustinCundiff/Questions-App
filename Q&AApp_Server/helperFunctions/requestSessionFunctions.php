<?php

/**
	This function inputs a sessionId, and either a past sessionInfo structure or new session parameters and performs 
	a request for questions or answers. If the session info structure is present it will be used to make the request
	and the past session request parameters will be ignored.
	
	$qaType- Whether the request is for a question(0) or answer(1)
	$sessionId- The ID of the session the request is attached to
	$sessionInfo- A structure containing info for a past request (optional)(values: id,requestId,lastQaId,numRows) 
	$questionId- The question id of the question to load answers for (integer- optional- will be ignored if sessionInfo is present)
	$search- The search parameter to use while looking up quesitons (optional- will be ignored if sessionInfo is present)
	$category- The category to use while looking up quesions (integer- optional- will be ignored if sessionInfo is present)
	$sortOrder- The sort order to use while looking up questions- do not use with a search (integer- optional- will be ignored if sessionInfo is present)
	$outputPage- An array that the results will be saved to
	$lastPage- If this is the last page this variable will be set to 1
	
	returns- The number of rows loaded
*/
function requestSessionFunctionsPerformSearch($qaType,$sessionId,$sessionInfo,$questionId,$search,$category,$sortOrder,&$outputPage,&$lastPage)
{
	global $qaCon;
	$tempNumRows=0;//holds the number of rows loaded, this value will be returned	
	$tempTotalRows=0;//holds the total number of rows for the request generated
	$lastQaId=0;//the last qaId that was loaded on the page
	$sessionRequestId=0;//the id of the row from session_request that must be updated to reflect the new lastQaId
	$requestId=0;//if data was newly created this is the requestId for it
	$requestType=0;//if data was newly created this is the requestType for it

	//first try to continue the search from a previous record
	if ($sessionInfo!=NULL)
	{
		$tempNumRows=requestFunctionsLoadPage($sessionInfo['requestId'],$qaType,$sessionInfo['lastQaId'],$outputPage);
		//we find the lastQaId by adding the previous lastQaId to the number of rows loaded
		$lastQaId=$sessionInfo['lastQaId']+$tempNumRows;
		//check if we're on the last page (add one since order indexing starts at 0)
		if (($lastQaId+1)>=$sessionInfo['numRows'])
		{
			$lastPage=1;	
		}
		//update lastQaId	
		$sessionRequestId=$sessionInfo['id'];
		mysql_query_log($qaCon,"UPDATE session_request SET lastQaId={$lastQaId} WHERE id={$sessionRequestId}","QA_App","requestSessionFunctions.php","requestSessionFunctionsPerformSearch()");
	}
	else
	{
		$tempNumRows=requestFunctionsPullId($qaType,$questionId,$search,$category,$sortOrder,$outputPage,$tempTotalRows,$requestId);
		//check if we're on the last page
		if ($tempNumRows>=$tempTotalRows)
		{
			$lastPage=1;	
		}
		//get the request type
		if ($qaType==0)
		{
			if ($search=='')
			{
				$requestType=1;
			}
			else
			{
				$requestType=2;
			}
		}
		else
		{
			$requestType=3;
		}
		//we subtract one to make up for the off by 1 problem
		$lastQaId=$tempNumRows-1;
		//delete any old sessions
		mysql_query_log($qaCon,"UPDATE session_request SET deleted=1 WHERE sessionId={$sessionId} and requestType={$requestType} and deleted=0","QA_App","requestSessionFunctions.php","requestSessionFunctionsPerformSearch()");
		//save the new session
		mysql_query_log($qaCon,"INSERT INTO session_request (sessionId,requestId,lastQaId,requestType,numRows) VALUES ({$sessionId},{$requestId},{$lastQaId},{$requestType},{$tempTotalRows})","QA_App","requestSessionFunctions.php","requestSessionFunctionsPerformSearch()");
	}
	
	return $tempNumRows;
}

/**
	This function checks if if an open session request corresponds to an inputted sessionId
	and request type. If a matching session is found a session request structure will be
	returned which can be passed to other session functions load load data. 
	
	$sessionId- The id of the session to load a session request for (integer)
	$requestType- the type of request to load session info for (1=quesiton category request, 2=question search request, 3=answer request)
	
	returns a structure that can be used to complete a continue request (id,requestId,lastQaId,numRows) or NULL if no session was found
*/
function requestSessionFunctionsPullSessionData($sessionId,$requestType)
{
	global $qaCon;
	
	$sessionInfoQuery=NULL;//used to hold the query for the session info
	$sessionInfo=NULL;//used to hold all the information the mysql query returns

	$sessionInfoQuery=mysql_query_log($qaCon,"SELECT id,requestId,lastQaId,numRows FROM session_request WHERE sessionId={$sessionId} and requestType={$requestType} and deleted=0","QA_App","requestSessionFunctions.php","requestSessionFunctionsPullSessionData()");
	$sessionInfo=mysqli_fetch_array($sessionInfoQuery);
	
	//return null if a previous query could not be found
	if ($sessionInfo['id']=='')
	{
		return NULL;
	}
	else
	{
		return $sessionInfo;
	}
}


?>