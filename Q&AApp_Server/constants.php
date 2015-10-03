<?php
include "environmentConstants.php";//include any constants that change depeinding on the environment

//general constants
define("END_MSG","\0");
define("MAX_RET",4);
define("MAX_IMG_SZ",2000000);//max image size
define("MAX_VID_SZ",2000000);//max video size
define("MAX_AUD_SZ",2000000);//max audio size
define("IMG_END",".jpg");
define("VID_END",".mp4");
define("AUD_END",".mp3");
define("FILE_DIR","files/");
define("ANS_THUMB_DIR","answerThumbnail/");
define("QUES_THUMB_DIR","questionThumbnail/");
define("QUES_PAGE_SZ",20);//number of questions that will be loaded per page query
define("ANS_PAGE_SZ",10);//number of answers that will be loaded per page query
define("QUES_TOTAL_LIMIT",1000);//the total number of question that can be loaded per query
//hardcoded off define("ANS_TOTAL_LIMIT",-1);//the total number of answers that can be loaded per query (-1= no limit)
define("QUES_REFRESH_TIME",300);//in seconds
define("ANS_REFRESH_TIME",300);//in seconds

//return values
define("ERROR","error");
define("ERROR_ID","id");
define("ERROR_MSG","message");
define("RESULTS","results");
define("RET0",2);
define("RET1",3);
define("RET2",4);

//messages
define("MSG_LOGIN",1);
define("MSG_CREATQUES",2);
define("MSG_GETQUESLIST",3);
define("MSG_GETQUES",4);
define("MSG_CREATANS",5);
define("MSG_GETANSLIST",6);
define("MSG_GETANS",7);
define("MSG_VOTEANS",8);
define("MSG_VOTEBEST",9);

//Errors
define("ERR_NONE",0);
define("ERR_SESSION",1);
define("ERR_MISSINFO",2);
define("ERR_BADINFO",3);
define("ERR_BADACCT",4);
define("ERR_BADENCRYPT",5);
define("ERR_VERSION",6);
define("ERR_BADMSG",7);
define("ERR_SERVER",8);
define("ERR_WRSIZE",9);
define("ERR_SEARCHCONFLICT",10);
define("ERR_INVALIDID",11);
define("ERR_EXTRAINFO",12);
define("ERR_WRTYPE",13);
define("ERR_NOJSON",14);
define("ERR_BADJSON",15);
define("ERR_VOTEBA",16);

?>
