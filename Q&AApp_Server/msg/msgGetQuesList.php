<?php
include_once "helperFunctions/requestFunctions.php";
include_once "helperFunctions/requestSessionFunctions.php";

function msgGetQuesList($categoryIn,$search,$modeIn,$continueIn)
{

	global $qaCon;//contains the mysql connection for the qa app
	global $date;//contains the epoch timestamp for the current time
	global $debug;//determines if debugging mode is on
	global $sessionId;//determines if a session is currently in use
		
	$output=Array();//the entire message output will be stored here
	$results=Array();//the message results will be stored here
	
	$questionOut;//this variable will contain the output information for questions
	$questionList=NULL;//stores the mysql output
	$question=0;//stores an array with information about a question
	
	$sessionRequest=NULL;//structure that holds information which can be used to make a continue request
		
	$numResults=0;//the number of questions returned by the MySql query
	$lastPage=0;//whether or not this is the last page/request for questions. (1=it is the last page,0=it is not the last page)
	
	
	//format variables
	$category=intval($categoryIn);
	$continue=intval($continueIn);
	$mode=intval($modeIn);
	
	
	
	
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
		//sort order should only be inlcuded if search is not used
		if ($search=="")
		{
			//mode is missing (if the search is being continued from a previous query the mode can be left empty)
			if ($modeIn==""){
				if ($debug==1){
					$output[ERROR]=createError(ERR_MISSINFO,"Missing mode variable");
				}else{
					$output[ERROR]=createError(ERR_MISSINFO,"");
				}
				return $output;
			}
			//mode is in the incorrect format
			if ($mode!=0 && $mode!=1){
				if ($debug==1){
					$output[ERROR]=createError(ERR_BADINFO,"Mode variable is not 1 or 0");
				}else{
					$output[ERROR]=createError(ERR_BADINFO,"");
				}
				return $output;
			}
		} 
		else
		{
			if ($modeIn!='')
			{
				if ($debug==1){
					$output[ERROR]=createError(ERR_EXTRAINFO,"A sortOrder should not be included if a search is being made");
				}else{
					$output[ERROR]=createError(ERR_EXTRAINFO,"");
				}
				return $output;
			}
		}
	} 
	//all values must be empty if continue==1 or continue==2
	else if ($continue==1 || $continue==2)
	{
		if ($modeIn!='')
		{
			if ($debug==1){
				$output[ERROR]=createError(ERR_EXTRAINFO,"sortOrder should not be included when continuePrev>0");
			}else{
				$output[ERROR]=createError(ERR_EXTRAINFO,"");
			}
			return $output;

		}
		if ($categoryIn!='')
		{
			if ($debug==1){
				$output[ERROR]=createError(ERR_EXTRAINFO,"A category type should not be included when continuePrev>0");
			}else{
				$output[ERROR]=createError(ERR_EXTRAINFO,"");
			}
			return $output;
		}
		if ($search!='')
		{
			if ($debug==1){
				$output[ERROR]=createError(ERR_EXTRAINFO,"A search term should not be included when continuePrev>0");
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
			$output[ERROR]=createError(ERR_BADINFO,"continuePrev variable is not 0,1, or 2");
		}else{
			$output[ERROR]=createError(ERR_BADINFO,"");
		}
		return $output;
	}
	
	




	////////////////////////////////
	//LOADING PREVIOUS SEARCH INFO//
	////////////////////////////////
	
	//get information from the previous search
	if ($continue>0)
	{
		//check if there was a previous session to continue from and get its data ($continue value corresponds to the requestType value)
		$sessionRequest=requestSessionFunctionsPullSessionData($sessionId,$continue);
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
	$numResults=requestSessionFunctionsPerformSearch(0,$sessionId,$sessionRequest,0,$search,$category,$mode,$questionList,$lastPage);
	$results['numQuestions']=$numResults;
	$results['lastPage']=$lastPage;

	//parse the query output
	for ($i=0;$questionList[$i]!=NULL;$i++)
	{	
		$question=$questionList[$i];
		$questionOut=Array();
		$questionOut['id']=intval($question['id']);
		$questionOut['title']=dattocon($question['title']);
		$questionOut['category']=intval($question['category']);
		$questionOut['numAnswers']=intval($question['answers']);
		$questionOut['thumbnailType']=intval($question['thumbnail']);
		//translate the thumbnail ID to the end user values
		//server ID | JSON value | type
		//-----------------------------
		//   -2     |    0       | no thumbnail
		//   -1     |    2       | custom thumbnail
		//   >-1    |    1       | thumbnail processing
		if ($questionOut['thumbnailType']==-2)
		{
			$questionOut['thumbnailType']=0;
		}
		else if ($questionOut['thumbnailType']==-1)
		{
			$questionOut['thumbnailType']=2;
			//if custom thumbnail also send the thumbnail location	
			$questionOut['thumbnail']=ROOT_URL.QUES_THUMB_DIR.intval($question['id']).IMG_END;
		} 
		else if ($questionOut['thumbnailType']>-1)
		{
			$questionOut['thumbnailType']=1;
		}
		$results['questions'][]=$questionOut;
	}

	
	
	

	//return the question list
	$output[RESULTS]=$results;
	return $output;
}

?>