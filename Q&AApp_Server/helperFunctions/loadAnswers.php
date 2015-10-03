<?php
include_once "helperFunctions/requestFunctions.php";
include_once "helperFunctions/requestSessionFunctions.php";

/**
	This function loads a list of all the answers for a specified question.
	
	$sessionRequest- If continuing from a previous answer page this contains the info about the previous request
	$questionId- The id of the question to load answers for
	$lastPage- 0 will be stored here if this is the last page of results, 1 will be stored otherwise
	$numResults- The number of results returned will be stored here
	
	returns- An array of all the answers loaded
*/
function loadAnswersLoad($sessionRequest,$questionId,&$lastPage,&$numResults)
{

	global $sessionId;//determines if a session is currently in use
	$output=Array();//an array of all the answers loaded will be stored here
		
	$answerOut;//this variable will contain the output information for questions
	$answerList=NULL;//stores the mysql output
	$answer=0;//stores an array with information about a question
	
	$sessionRequest=NULL;//structure that holds information which can be used to make a continue request
		
	//////////////////////////////
	//PERFORM THE ACTUAL SEARCH//
	/////////////////////////////
	$numResults=requestSessionFunctionsPerformSearch(1,$sessionId,$sessionRequest,$questionId,'',0,0,$answerList,$lastPage);
	//$results['numAnswers']=$numResults;
	//$results['lastPage']=$lastPage;

	//parse the query output
	for ($i=0;$answerList[$i]!=NULL;$i++)
	{	
		$answer=$answerList[$i];
		$answerOut=Array();
		$answerOut['id']=intval($answer['id']);
		$answerOut['title']=dattocon($answer['title']);
		$answerOut['votes']=intval($answer['votes']);
		$answerOut['isBest']=intval($answer['isBest']);
		$answerOut['voteOn']=intval($answer['voteOn']);
		$answerOut['thumbnailType']=intval($answer['thumbnail']);
		//translate the thumbnail ID to the end user values
		//server ID | JSON value | type
		//-----------------------------
		//   -2     |    0       | no thumbnail
		//   -1     |    2       | custom thumbnail
		//   >-1    |    1       | thumbnail processing
		if ($answerOut['thumbnailType']==-2)
		{
			$answerOut['thumbnailType']=0;
		}
		else if ($answerOut['thumbnailType']==-1)
		{
			$answerOut['thumbnailType']=2;
			//if custom thumbnail also send the thumbnail location	
			$answerOut['thumbnail']=ROOT_URL.ANS_THUMB_DIR.intval($answer['id']).IMG_END;
		} 
		else if ($answerOut['thumbnailType']>-1)
		{
			$answerOut['thumbnailType']=1;
		}
		$output[]=$answerOut;
	}

	
	return $output;
}

?>