<?php
//user QA_APP
//pass AQ56759*
$includePath="../../";

include $includePath."coreFuncs.php";
include $includePath."mysqlHelpers.php";
include "logErrors.php";
include "qaMysql.php";

include "constants.php";
include "reqSetup.php";

include "msg/msgLogin.php";
include "msg/msgCreatQues.php";
include "msg/msgGetQuesList.php";
include "msg/msgGetQues.php";
include "msg/msgCreatAns.php";
include "msg/msgGetAnsList.php";
include "msg/msgGetAns.php";
include "msg/msgVoteAns.php";

//set the jason type for clients
header('Content-Type: application/json');

//verify the session is valid
loadSession($session);

//switch between messages
switch ($msgId)
{
	case MSG_LOGIN:
		$output=msgLogin(c($in['ver']),  c($in['username']),  c($in['pass']),  c($in['passEncrypt']),  c($in['debugOn']));
		echo json_encode($output, JSON_UNESCAPED_SLASHES);
		break;
		
	case MSG_CREATQUES:
		$output=msgCreatQues(c($in['title']),  c($in['category']) , $in['qaParts'],  $_FILES);
		echo json_encode($output, JSON_UNESCAPED_SLASHES);
		break;
		
	case MSG_GETQUESLIST:
		$output=msgGetQuesList(c($in['category']),  c($in['search']),  c($in['sortOrder']),  c($in['continuePrev']));
		echo json_encode($output, JSON_UNESCAPED_SLASHES);
		break;
	
	case MSG_GETQUES:
		$output=msgGetQues(c($in['questionId']),  c($in['listParts']), c($in['listAnswers']));
		echo json_encode($output, JSON_UNESCAPED_SLASHES);
		break;
		
	case MSG_CREATANS:
		$output=msgCreatAns(c($in['questionId']),  c($in['title']),  $in['qaParts'],  $_FILES);
		echo json_encode($output, JSON_UNESCAPED_SLASHES);
		break;
	
	case MSG_GETANSLIST:
		$output=msgGetAnsList(c($in['questionId']),  c($in['continuePrev']));
		echo json_encode($output, JSON_UNESCAPED_SLASHES);
		break;
		
	case MSG_GETANS:
		$output=msgGetAns(c($in['answerId']),  c($in['listParts']));
		echo json_encode($output, JSON_UNESCAPED_SLASHES);
		break;

	case MSG_VOTEANS:
		$output=msgVoteAns(c($in['answerId']),  c($in['vote']));
		echo json_encode($output, JSON_UNESCAPED_SLASHES);
		break;
		
	default:
		//NO JSON
		if ($inJson=="")
		{
			//no custom message since we weren't able to pull the debug value
			$output[ERROR]=createError(ERR_NOJSON,"");
		}
		//BAD JSON
		else if ($in==NULL)
		{
			//no custom message since we weren't able to pull the debug value
			$output[ERROR]=createError(ERR_BADJSON,"");
		}
		//BAD ID
		else
		{
			if ($debug)
			{
				$output[ERROR]=createError(ERR_BADMSG,"Invalid msgId! msgId is ".$msgId);
			}
			else
			{
				$output[ERROR]=createError(ERR_BADMSG,"");
			}
		}
		echo json_encode($output, JSON_UNESCAPED_SLASHES);
		break;
}	


?>
