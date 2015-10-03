<?php

function msgLogin($verIn,$username,$rawPass,$encryptPass,$debug)
{

	global $qaCon;
	global $date;
	$output=Array();//the entire message output will be stored here
	$results=Array();//the message results will be stored here
	$userId=0;
	$sessionKey;
	$sessionId;
	
	//format variables
	$ver=intval($verIn);
	$debug=intval($debug);
	
	//check variable formatting
	//debug value is wrong
	if ($debug!=1 && $debug!=0){
		$output[ERROR]=createError(ERR_BADINFO,"");;
		return $output;
	}
	//missing password
	if ($rawPass=="" && $encryptPass==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing password");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return $output;
	}
	//missing version
	if ($verIn==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing version");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return $output;
	}
	//missing version
	if ($username==""){
		if ($debug==1){
			$output[ERROR]=createError(ERR_MISSINFO,"Missing username");
		}else{
			$output[ERROR]=createError(ERR_MISSINFO,"");
		}
		return $output;
	}
	//version is wrong
	if ($ver==0){
		if ($debug==1){
			$output[ERROR]=createError(ERR_BADINFO,"Version is 0");
		}else{
			$output[ERROR]=createError(ERR_BADINO,"");
		}
		return $output;
	}
	
	//check version
	//for now we do not have a version check, but this will be implemented as the game gets new versions
	
	//check username and password
	//for now we auto accept
	$userId=1;
	
	//create a session
	$sessionKey=randString(5);
	mysql_query_log($qaCon,"INSERT INTO session (sessionKey,userId,date,debug) VALUES ('{$sessionKey}',{$userId},{$date},{$debug})","QA_App","msgLogin.php","msgLogin()");
	$sessionId=mysqli_insert_id($qaCon);
	
	//format session id
	if ($sessionId<100)
	{
		if ($sessionId>9)
		{
			$sessionId="0".$sessionId;
		}
		else
		{
			$sessionId="00".$sessionId;
		}
	}
	
	//return session info
	$results['sessionKey']=$sessionId.$sessionKey;
	$output[RESULTS]=$results;
	return $output;
}

?>