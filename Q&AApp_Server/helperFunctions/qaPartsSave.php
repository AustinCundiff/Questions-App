<?php 

/*
	This part verifies that qaPart information is correct based on the data inputted
	through $partsIn and $file. If the information is correct will will be restructured
	and saved into $qaParts. The function will also save the id of the thumbnail image to $tumbnailId
	
	$partsIn- a JSON array of inputted parts
	$file- a HTTP_FILE array of inputted files
	$tumbnailId- the id of the picture/video that was selected as the tumbnail will be saved here
	$qaParts- a reformeatted version of the qaParts will be saved here, this structure can be  later passed to qaPartsSave
	
	retruns- An array containing error output or NULL if there were no errors
*/
function qaPartsSaveVerify($partsIn,$file,&$thumbnailId,&$qaParts)
{
	global $debug;
	
	$output=Array();//if there are any errors they will be outputted using this structure

	$tmpPartType=0;//temporarily holds a part type
	$tmpPartId=0;//temporarily holds a mysql part ID (returned after inserting part in database)
	$tmpPartThumbnail=0;//temporarily holds if part is the question thumbnail (0=no, 1=yes)
	$tmpPartText="";//temporarily holds the part text (only used if partType=1)
	$tmpPartFile="";//temporarily holds the temporary holding location for the part file (not used when partType=1)
	$tmpPartFileEnd="";//temporarily holds the .X extension for the part's file
	
	$numParts=0;//holds the number of parts in the question
	$partId=Array();//holds all the part ids after they've been added to the mysql database
	$partType=Array();//holds all the part types
	$partText=Array();//holds the text for every part (used for text parts (partType=1 & later orientation data for image parts (partType=2))
	$partFile=Array();//holds the temporary file for every part (not used when partType=1)
	$partFileEnd=Array();//holds the .X extension for every part  (not used when partType=1)
	
	//load question sections & verify that the information in each part is correct
	$i=0;
	for (;$partsIn[$i]!=NULL;$i++)
	{
		//pull the part info from the JSON object
		$tmpPartType=intval($partsIn[$i]['partType']);
		$tmpPartThumbnail=intval($partsIn[$i]['thumbnail']);
		$tmpPartText=c($partsIn[$i]['text']);
		$tmpPartFile=c($partsIn[$i]['fileName']);
		
		//-----------------
		//missing part type
		//-----------------
		if ($tmpPartType==0)
		{
			if ($debug==1){
				$output[ERROR]=createError(ERR_MISSINFO,"Missing partType for part #".$i);
			}else{
				$output[ERROR]=createError(ERR_MISSINFO,"");
			}
			return $output;
		}
		
		//-------------------------------
		//complete checks for a text part
		//-------------------------------
		else if ($tmpPartType==1)
		{
			if ($tmpPartThumbnail==1)
			{
				if ($debug==1){
					$output[ERROR]=createError(ERR_BADINFO,"A text part cannot be set as the thumbnail part #".$i);
				}else{
					$output[ERROR]=createError(ERR_BADINFO,"");
				}
				return $output;
			}
			if ($tmpPartText=="")
			{
				if ($debug==1){
					$output[ERROR]=createError(ERR_MISSINFO,"Missing text for part #".$i);
				}else{
					$output[ERROR]=createError(ERR_MISSINFO,"");
				}
				return $output;
			}
			if ($tmpPartFile!="")
			{
				if ($debug==1){
					$output[ERROR]=createError(ERR_EXTRAINFO,"fileName not needed for part #".$i);
				}else{
					$output[ERROR]=createError(ERR_EXTRAINFO,"");
				}
				return $output;
			}
			//save the required values
			$partText[$i]=$tmpPartText;
		}
		
		//---------------------------------
		//complete checks for an image part
		//---------------------------------
		else if($tmpPartType==2)
		{
			if (!qaPartsSaveFileChecks($i,$tmpPartText,$tmpPartFile,$file,MAX_IMG_SZ,"image/jpeg",$tmpPartThumbnail,$thumbnailId,$output))
			{
				return $output;
			}
			//save the required values
			if ($tmpPartThumbnail==1)
			{
				$thumbnailId=$i;
			}
			$partFile[$i]=$file[$tmpPartFile]['tmp_name'];
			$partFileEnd[$i]=IMG_END;
		}

		
		//---------------------------------
		//complete checks for an video part
		//---------------------------------
		else if($tmpPartType==3)
		{
			if (!qaPartsSaveFileChecks($i,$tmpPartText,$tmpPartFile,$file,MAX_VID_SZ,"video/mp4",$tmpPartThumbnail,$thumbnailId,$output))
			{
				return $output;
			}
			//save the required values
			if ($tmpPartThumbnail==1)
			{
				$thumbnailId=$i;
			}
			$partFile[$i]=$file[$tmpPartFile]['tmp_name'];
			$partFileEnd[$i]=VID_END;
		}

		
		//---------------------------------
		//complete checks for an audio part
		//---------------------------------
		else if($tmpPartType==4)
		{
			if ($tmpPartThumbnail==1)
			{
				if ($debug==1){
					$output[ERROR]=createError(ERR_BADINFO,"An audio part cannot be set as the thumbnail part #".$i);
				}else{
					$output[ERROR]=createError(ERR_BADINFO,"");
				}
				return $output;
			}
			if (!qaPartsSaveFileChecks($i,$tmpPartText,$tmpPartFile,$file,MAX_AUD_SZ,"audio/mpeg3",$tmpPartThumbnail,$thumbnailId,$output))
			{
				return $output;
			}
			//save the required values
			$partFile[$i]=$file[$tmpPartFile]['tmp_name'];
			$partFileEnd[$i]=AUD_END;
		}

		//--------------------------------------
		//GIVE AN ERROR FOR PART #s OUT OF RANGE
		//--------------------------------------
		else
		{
			if ($debug==1){
				$output[ERROR]=createError(ERR_BADINFO,"partType must be between 1 and 4 part #".$i);
			}else{
				$output[ERROR]=createError(ERR_BADINFO,"");
			}
			return $output;
		}

		
		//save the part type if it passes all the checks
		$partType[$i]=$tmpPartType;
	}
	$numParts=$i;//increment the number of parts by 1 since the counting was done from 0

	//save all the information we just pulled to the qaParts structure
	$qaParts['numParts']=$numParts;
	$qaParts['partId']=$partId;
	$qaParts['partType']=$partType;
	$qaParts['partText']=$partText;
	$qaParts['partFile']=$partFile;
	$qaParts['partFileEnd']=$partFileEnd;

	//if no errors occured return NULL
	return NULL;
}














/*
	This function saves all the data in the $qaParts array to the database.
	
	$qaParts- The data to save, this array should be created by the qaPartsVerifty function
	$qaType- Whether the part is linked to an answer or question (0=question, 1= answer)
	$qaId- The id of question/answer the part is linked to
	
	retruns- An array containing error output or NULL if there were no errors
*/
function qaPartsSaveSave($qaParts,$qaType,$qaId)
{
	global $qaCon;
	global $debug;

	$output=Array();//if there are any errors they will be outputted using this structure

	$tmpPartType=0;//temporarily holds a part type
	$tmpPartId=0;//temporarily holds a mysql part ID (returned after inserting part in database)
	$tmpPartText="";//temporarily holds the part text (only used if partType=1)
	$tmpPartFileEnd="";//temporarily holds the .X extension for the part's file
	
	//pull all the question parts out of the inputted structure
	$numParts=$qaParts['numParts'];
	$partId=$qaParts['partId'];
	$partType=$qaParts['partType'];
	$partText=$qaParts['partText'];
	$partFile=$qaParts['partFile'];
	$partFileEnd=$qaParts['partFileEnd'];

	
	//save all the parts to the database
	for ($i=0;$i<$numParts;$i++)
	{
		//pull the part info from the arrays created earlier, this makes it easier to pass data into the mysql request
		$tmpPartType=$partType[$i];
		$tmpPartText=$partText[$i];
		$tmpPartFileEnd="";
		
		//save part info to the server, we use $i for the block order as the blocks will be added in the correct order
		mysql_query_log($qaCon,"INSERT INTO qa_parts (qaType,qaIndex,partType,blockOrder,data) VALUES ({$qaType},{$qaId},{$tmpPartType},{$i},'{$tmpPartText}')","QA_App","qaParts.php","qaPartsSave()");
		$tmpPartId=mysqli_insert_id($qaCon);	
		$partId[$i]=$tmpPartId;

		//save file if multimedia
		if ($partType[$i]>1)
		{
			if (!copy($partFile[$i],FILE_DIR.$tmpPartId.$partFileEnd[$i]))
			{
				//clear the recent records from the server since they didn't finish saving
				for ($j=$i;$j>=0;$j--)
				{
					$tmpPartId=$partId[$j];				
					mysql_query_log($qaCon,"DELETE FROM qa_parts WHERE id={$tmpPartId}","QA_App","qaParts.php","qaPartsSave()");
				}
				logError(LOG_SEVERE,"QA_App","qaParts.php","qaPartsSave()","Could not copy file for part ".$tmpPartId." & full save path:".(FILE_DIR.$tmpPartId.$partFileEnd[$i])." & temporary dir:".$partFile[$i]." check file permissions");
				
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
		}
	}
	
	//if no errors occured return NULL
	return NULL;

}















//does the generic file checks to make sure the user uploaded a file (does not check the file size or type)
//on success (no errors) the function returns 1, otherwise it will return 0
function qaPartsSaveFileChecks($pn,$text,$fileName,$fileArray,$maxSize,$fileType,$thumbnailOn,$currentThumbnail,&$output)
{
	global $debug;
	$finfoType=0;//will temporarily hold the ID of the fifinfo mime id for mime checking
	
	if ($thumbnailOn==1 && $currentThumbnail!=-2)
	{
		if ($debug==1){
			$output[ERROR]=createError(ERR_BADINFO,"Part number ".$currentThumbnail." has already been set as the thumbnail, another thumbnail cannot be set for #".$pn);
		}else{
			$output[ERROR]=createError(ERR_BADINFO,"");
		}
		return false;
	}
	if ($fileName=="")
	{
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing fileName for part #".$pn);
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return false;
	}
	if ($text!="")
	{
		if ($debug==1){
			$output[ERROR]=createError(ERR_EXTRAINFO,"text not needed for part #".$pn);
		}else{
			$output[ERROR]=createError(ERR_EXTRAINFO,"");
		}
		return false;
	}
	//check file 
	if ($fileArray[$fileName]['tmp_name']=="")
	{
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing file for part #".$pn." (fileName is ".$fileName.")");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return false;
	}
	if (fileSize($fileArray[$fileName]['tmp_name'])<1)
	{
		if ($debug==1){
			$output[ERROR]=createError(ERR_WRSIZE,"File smaller than 1 byte for part #".$pn." (current size is ".fileSize($fileArray[$fileName]['tmp_name']).")");
		}else{
			$output[ERROR]=createError(ERR_WRSIZE,"");
		}
		return false;
	}
	if (fileSize($fileArray[$fileName]['tmp_name'])>$maxSize)
	{
		if ($debug==1){
			$output[ERROR]=createError(ERR_WRSIZE,"File too large for part #".$pn."(".$maxSize." max, current size is ".fileSize($fileArray[$fileName]['tmp_name']).")");
		}else{
			$output[ERROR]=createError(ERR_WRSIZE,"");
		}
		return false;
	}
	$finfoType = finfo_open(FILEINFO_MIME_TYPE);
	if ($fileType!="audio/mpeg3")
	{
		//check type of non-mp3 files
		if (finfo_file($finfoType,$fileArray[$fileName]['tmp_name'])!=$fileType)
		{
			if ($debug==1){
				$output[ERROR]=createError(ERR_WRTYPE,"File is of wrong type for part #".$pn."(must be ".$fileType.", current type is ".finfo_file($finfoType,$fileArray[$fileName]['tmp_name']).")");
			}else{
				$output[ERROR]=createError(ERR_WRTYPE,"");
			}
			return false;
		}
	}
	else
	{
		//check type of mp3 files (finfo might not return mp3)
		if (($fileArray[$fileName]['type']!=$fileType && $fileArray[$fileName]['type']!="audio/mp3") || (finfo_file($finfoType,$fileArray[$fileName]['tmp_name'])!="application/octet-stream" && finfo_file($finfoType,$fileArray[$fileName]['tmp_name'])!=$fileType))
		{
			if ($debug==1){
				$output[ERROR]=createError(ERR_WRTYPE,"File is of wrong type for part #".$pn."(must be ".$fileType.", current type is ".$fileArray[$fileName]['type'].":".finfo_file($finfoType,$fileArray[$fileName]['tmp_name']).": MP3 Check)");
			}else{
				$output[ERROR]=createError(ERR_WRTYPE,"");
			}
			return false;
		}
	}
	
	return true;
}	
?>
