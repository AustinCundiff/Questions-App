<?php
include "../../../coreFuncs.php";
include "../logErrors.php";
include "../../../mysqlHelpers.php";
include "../qaMysql.php";
mysql_query_log($qaCon,"ALTER request_info MODIFY search varchar(100) DEFAULT NULL;","QA_App","resetDatabase.php","");
/*
mysql_query_log($qaCon,"DELETE FROM answer_votes;","QA_App","resetDatabase.php","");
mysql_query_log($qaCon,"ALTER TABLE answer_votes AUTO_INCREMENT = 1;","QA_App","resetDatabase.php","");

mysql_query_log($qaCon,"DELETE FROM answers;","QA_App","resetDatabase.php","");
mysql_query_log($qaCon,"ALTER TABLE answers AUTO_INCREMENT = 1;","QA_App","resetDatabase.php","");

mysql_query_log($qaCon,"DELETE FROM files;","QA_App","resetDatabase.php","");
mysql_query_log($qaCon,"ALTER TABLE files AUTO_INCREMENT = 1;","QA_App","resetDatabase.php","");

mysql_query_log($qaCon,"DELETE FROM qa_parts;","QA_App","resetDatabase.php","");
mysql_query_log($qaCon,"ALTER TABLE qa_parts AUTO_INCREMENT = 1;","QA_App","resetDatabase.php","");

mysql_query_log($qaCon,"DELETE FROM questions;","QA_App","resetDatabase.php","");
mysql_query_log($qaCon,"ALTER TABLE questions AUTO_INCREMENT = 1;","QA_App","resetDatabase.php","");

mysql_query_log($qaCon,"DELETE FROM session;","QA_App","resetDatabase.php","");
mysql_query_log($qaCon,"ALTER TABLE session AUTO_INCREMENT = 1;","QA_App","resetDatabase.php","");

mysql_query_log($qaCon,"DELETE FROM session_search;","QA_App","resetDatabase.php","");
mysql_query_log($qaCon,"ALTER TABLE session_search AUTO_INCREMENT = 1;","QA_App","resetDatabase.php","");
*/
?>


<h1>Reset Done!</h1>
<h3>Remember to delete old files</h3>