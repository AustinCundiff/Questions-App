<?php
/**
	This function gets the request ID for a specified set of parameters and returns it.
	The funciton also returns an array of all the results on the first page of the request.
	
	$qaType- Whether the request is for a question or answer (0=question,1=answer)
	$questionId- If for answers, the id of the question to load answers for (integer)
	$search- If for questions, the term to search for in questions
	$category- If for question, the category the questions are in (integer)
	$sortOrder-If for questions, the sort order the questions are in (integer- do not use with search)
	$firstPage- The first page of results wil be stored here
	$totalRows- The total number rows in the entire request will be stored here if a new request was created
	$requestId- The request ID will be stored here
	
	returns- The number of results in the first page
*/
function requestFunctionsPullId($qaType,$questionId,$search,$category,$sortOrder,&$firstPage,&$totalRows,&$requestId)
{
	//first look for an existing request id
	$requestId=requestFunctionsLookupRequest($qaType,$questionId,$search,$category,$sortOrder,$totalRows);
	if ($requestId==-1)
	{
		//if that fails create a new request id
		$numRows=requestFunctionsCreateRequest($qaType,$questionId,$search,$category,$sortOrder,$firstPage,$requestId,$totalRows);
	}
	else
	{
		//if we have an existing request id load a the first page of requests (-1 is used since we haven't loaded any results yet (not even 0)
		$numRows=requestFunctionsLoadPage($requestId,$qaType,-1,$firstPage);
	}
	
	return $numRows;
}













/**
	This function takes a set of input parameters and uses them to check if there
	are any existing requests with the same information. If there is an existing request
	the function will return its id, if there are not existing 	requests the function will
	 return -1
	 
	$qaType- Whether the inputted info is or a question (qaType=0) or answer (qaType=1)
	$questionId- If an answer- the question the answer is for(integer)
	$search- If a quesiton- the search term used (if any-must be 100 character or less)
	$category- If a question- the category that the questions are in(integer)
	$sortOrder- If a question- the sort order used for the questions (integer)
	$totalRows- The total number of rows in the request 
	
	returns the requestId for the new request or -1 if no request exists

*/
function requestFunctionsLookupRequest($qaType,$questionId,$search,$category,$sortOrder,&$totalRows)
{
	global $date;
	global $qaCon;
	
	$minDate=0;//the minimum date timestamp (in seconds) that a request and still be valid (the oldest it can be)
	$lookupResponse=NULL;
	$lookupInfo=NULL;
	
	if ($qaType==0)
	{
		$minDate=$date-QUES_REFRESH_TIME;
	}
	else
	{
		$minDate=$date-ANS_REFRESH_TIME;
	}
	
	$lookupResponse=mysql_query_log($qaCon,"SELECT id,numRows FROM request_info WHERE qaType={$qaType} and questionId={$questionId} and search='{$search}' and category={$category} and sortOrder={$sortOrder} and date>{$minDate};","QA_App","requestFunctions.php","requestFunctionsLookupRequest()");
	$lookupInfo=mysqli_fetch_array($lookupResponse);
	
	//return -1 if no id is found
	if ($lookupInfo['id']=='')
	{
		return -1;
	}
	else
	{		
		$totalRows=$lookupInfo['numRows'];
		return $lookupInfo['id'];
	}
}














/**
	This function inputs request information and creates a new request in the database(that can
	be accessed later) based on that information. The function also returns the requestId and the
	first page of query results, which can be sent to the user.
	
	$qaType- Whether the inputted info is or a question (qaType=0) or answer (qaType=1)
	$questionId- If an answer- the question the answer is for(integer)
	$search- If a quesiton- the search term used (if any-must be 100 character or less)
	$category- If a question- the category that the questions are in(integer)
	$sortOrder- If a question- the sort order used for the questions (integer)
	$firstPage- An array of all the IDs on the first page will be saved here
	$requestId- the requestId for the new request
	$totalRows- The total number of rows in the entire request will be stored here
	
	returns the number of rows in the first page
*/
function requestFunctionsCreateRequest($qaType,$questionId,$search,$category,$sortOrder,&$firstPage,&$requestId,&$totalRows)
{
	global $qaCon;
	global $date;
	
	//create a request_info record to identify the request	
	mysql_query_log($qaCon,"INSERT INTO request_info (qaType,questionId,search,category,sortOrder,date) VALUES ({$qaType},{$questionId},'{$search}',{$category},{$sortOrder},{$date});","QA_App","requestFunctions.php","requestFunctionsCreateRequest()");
	$requestId=mysqli_insert_id($qaCon);
	
	//create a list of all the questions/answers in the list
	if ($qaType==0)
	{
		//question request
		$numRows=requestFunctionsCreateQuestionRequest($search,$category,$sortOrder,$requestId,$firstPage,$totalRows);	
	}
	else
	{
		//answer request
		$numRows=requestFunctionsCreateAnswerRequest($questionId,$requestId,$firstPage,$totalRows);
	}

	//save the number of rows to the new request
	mysql_query_log($qaCon,"UPDATE request_info SET numRows={$totalRows} WHERE id={$requestId};","QA_App","requestFunctions.php","requestFunctionsCreateRequest()");

	
	return $numRows;
}
















/**
	This function completes a query of questions based on the inputted information.
	Saves that query to the database and returns the first page of query results.
	
	$search- The search term to use (if any- must be 100 characters or less)
	$category- The category the question is in (0=no category- integer)
	$sortOrder- The sort order to use (integer)
	$requestId- The reuqest ID to save the results under(integer)
	$firstPage- an array of all the question IDs on the first page
	$totalRows- The total number of rows loaded for the whole request will be saved here
	
	returns- The number of rows on the first page
*/
function requestFunctionsCreateQuestionRequest($search,$category,$sortOrder,$requestId,&$firstPage,&$totalRows)
{
	global $qaCon;

	$numRows=0;//the number of rows on the first page
	$questionId=0;//a temporary holding place for the question id before it is inserted into the ordered request list
	$indexCounter=0;//a counter to keep track of the numbering of each question for ordering purposes
	$queryOrder=NULL;//mysql code for the order the question will be in
	$query=NULL;//the query that will load all the question
	$questions=NULL;//a list of all the questions returned
	$question=NULL;//tempoararily holds a single question so it can be returned
	$quesTotalLimit=QUES_TOTAL_LIMIT;//the number of questions that can be loaded per request

	//////////////////////////////
	//PERFORM THE ACTUAL SEARCH//
	/////////////////////////////
	//create a query depending on the mode
	if ($sortOrder==0)
	{
		$queryOrder="ORDER BY date DESC";
	}
	else
	{
		$queryOrder="ORDER BY answers DESC";
	}
	
	//create the query
	$query="SELECT id,
				   title,
				   category,
				   answers,
				   thumbnail
			FROM questions
			WHERE 	  ({$category}=0
					  	   OR category={$category})
				  AND ('{$search}'=''
				  		   OR MATCH(title) AGAINST('{$search}' IN BOOLEAN MODE))
			".$queryOrder."
			LIMIT {$quesTotalLimit};
			";
	
	//run the query
	$questions=mysql_query_log($qaCon,$query,"QA_App","requestFunctions.php","requestFunctionsCreateQuestionRequest()");

	//save all the IDs and their order to the database and a size limited array that will be sent to the user
	while ($question=mysqli_fetch_array($questions))
	{
		$questionId=$question['id'];	
		mysql_query_log($qaCon,"INSERT INTO request_list (requestId,qaId,qaOrder) VALUES ({$requestId},{$questionId},{$indexCounter});","QA_App","requestFunctions.php","requestFunctionsCreateQuestionRequest()");
		if ($indexCounter<QUES_PAGE_SZ)
		{
			$firstPage[$indexCounter]=$question;
			$numRows++;
		}
		$indexCounter++;
	}
	
	$totalRows=$indexCounter;
	return $numRows;
}
















/**
	This function completes a query of answers based on the inputted information.
	Saves that query to the database and returns the first page of query results.
	
	$questionId- The id of the question to load answers for (integer)
	$requestId- The reuqest ID to save the results under(integer)
	$firstPage- an array of all the question IDs on the first page
	$totalRows- The total number of rows loaded for the whole request will be saved here
	
	returns- The number of rows on the first page
*/
function requestFunctionsCreateAnswerRequest($questionId,$requestId,&$firstPage,&$totalRows)
{
	global $qaCon;
	global $userId;
	
	$numRows=0;//the number of rows on the first page
	$indexCounter=0;//a counter to keep track of the numbering of each question for ordering purposes
	$query=NULL;//holds the query  that will load all the answers
	$answerId=0;//temporarily holds the answer id so it can be saved to the database
	$answers=NULL;//holds all the answers loaded
	$answer=NULL;//temporarily holds a single answer so it's info can be saved
	//for now answers do not have a query limit
	//$ansTotalLimit=ANS_TOTAL_LIMIT;//the number of questions that can be loaded per request

	//////////////////////////////
	//PERFORM THE ACTUAL SEARCH//
	/////////////////////////////	
	//create the query
	$query="SELECT answers.id id,
				   answers.title title,
				   answers.isBest isBest,
				   answers.votes votes,
				   answers.thumbnail thumbnail,
				   IF (answer_votes.userId IS NULL OR answer_votes.userId=0,0,1) voteOn
			  FROM answers
		 LEFT JOIN answer_votes ON answers.id=answer_votes.answerId
			 WHERE questionId={$questionId}
			  AND (answer_votes.userId IS NULL
				   OR answer_votes.userId={$userId})
		  ORDER BY isBest,votes DESC
			";
	
	//run the query
	$answers=mysql_query_log($qaCon,$query,"QA_App","requestFunctions.php","requestFunctionsCreateAnswerRequest()");

	//save all the IDs and their order to the database and a size limited array that will be sent to the user
	while ($answer=mysqli_fetch_array($answers))
	{
		$answerId=$answer['id'];	
		mysql_query_log($qaCon,"INSERT INTO request_list (requestId,qaId,qaOrder) VALUES ({$requestId},{$answerId},{$indexCounter});","QA_App","requestFunctions.php","requestFunctionsCreateQuestionRequest()");
		if ($indexCounter<ANS_PAGE_SZ)
		{
			$firstPage[$indexCounter]=$answer;
			$numRows++;
		}
		$indexCounter++;
	}
	
	$totalRows=$indexCounter;
	return $numRows;
}








/**
	This function loads input from a specified section of a saved result. The function
	returns the number of rows loaded and a page of results.
	
	$requestId- The request to load the results from
	$qaType- Whether the request is for a question (0) or answer (1)
	$pageOffset- the order id the sort should start from
	$page- A list of the results will be saved here
	
	returns- the number of rows read
*/
function requestFunctionsLoadPage($requestId,$qaType,$pageOffset,&$page)
{
	global $qaCon;
	global $userId;
	
	$returnIds=Array();//an array of all the IDs that will be returned because they need to be sent to the user
	$indexCounter=0;//a counter to keep track of the numbering of each question for ordering purposes
	$pageLimit=0;//the number of entries on a page, and how much we should limit the mysql query
	$qas=NULL;//a mysql structure of all the question/answer objects
	$qa=NULL;//a mysql structure of one question/answer object
	$query="";//the query that will be used to load the question/anser results
	
	if ($qaType==0)
	{
		//load questions
		$pageLimit=QUES_PAGE_SZ;
		$query="SELECT questions.id id,
					   questions.title title,
					   questions.category category,
					   questions.answers answers,
					   questions.thumbnail thumbnail
			      FROM request_list 
			      JOIN questions ON request_list.qaId=questions.id
			     WHERE requestId={$requestId} 
			       AND qaOrder>{$pageOffset} 
			  ORDER BY qaOrder 
			ASC LIMIT {$pageLimit}";
		$qas=mysql_query_log($qaCon,$query,"QA_App","requestFunctions.php","requestFunctionsLoadPage()");
	}
	else
	{
		//load answers";
		$pageLimit=ANS_PAGE_SZ;
		$query="SELECT answers.id id,
					   answers.title title,
					   answers.votes votes,
					   answers.isBest isBest,
					   answers.thumbnail thumbnail,
					   IF (answer_votes.userId IS NULL OR answer_votes.userId=0,0,1) voteOn
		          FROM request_list 
		          JOIN answers ON request_list.qaId=answers.id 
		     LEFT JOIN answer_votes ON answers.id=answer_votes.answerId
		         WHERE requestId={$requestId} 
		           AND qaOrder>{$pageOffset} 
		           AND (answer_votes.userId IS NULL 
		             	 OR answer_votes.userId={$userId})
		      ORDER BY qaOrder 
		    ASC LIMIT {$pageLimit}";
		$qas=mysql_query_log($qaCon,$query,"QA_App","requestFunctions.php","requestFunctionsLoadPage()");
	}
	
	
	while ($qa=mysqli_fetch_array($qas))
	{
		$returnIds[$indexCounter]=$qa;
		$indexCounter++;
	}

	$page=$returnIds;
	return $indexCounter;
}

?>