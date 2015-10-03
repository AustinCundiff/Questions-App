<?php

/**
	This function loads all the parts for a specific question or answer
	an returns an array containing information on each part. 
	
	$qaType- Whether the parts for an answer or question should be loaded (0=question, 1=answer)
	$qaId- The ID of the question or answer for which parts should be loaded
	
	returns- An array containing a list of arrays that represent each part. Each sub array will contain a part's partType, text, and filePath
*/
function qaPartsLoadLoad($qaType,$qaId)
{
	global $qaCon;

	$results=Array();//an array of all the loaded parts
	$partQuery=NULL;//this will contain a mysql structure containing all the loaded parts
	$partInfo=NULL;//this will contain a pre-processed array with information about a single part
	$part=NULL;//this will contain a processed array with information about a single part
	
	$partQuery=mysql_query_log($qaCon,"SELECT id,partType,data FROM qa_parts WHERE qaType={$qaType} and qaIndex={$qaId} ORDER BY blockOrder ASC","QA_App","qaPartsLoad.php","qaPartsLoadLoad()");
		
	while ($partInfo=mysqli_fetch_array($partQuery))
	{
		$part=Array();//clear the part info
		
		$part['partType']=$partInfo['partType'];
		
		switch ($part['partType'])
		{
			case 1:
				$part['text']=$partInfo['data'];
				break;
				
			case 2:
				$part['filePath']=ROOT_URL.'/'.FILE_DIR.$partInfo['id'].IMG_END;
				break;
				
			case 3:
				$part['filePath']=ROOT_URL.'/'.FILE_DIR.$partInfo['id'].VID_END;
				break;
				
			case 4:
				$part['filePath']=ROOT_URL.'/'.FILE_DIR.$partInfo['id'].AUD_END;
				break;
		}
		
		$results[]=$part;
	}
	
	return $results;
}



?>