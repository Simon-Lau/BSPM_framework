<?php

/**
 * example of script
 * update_t_user_info.php
 * script for update table t_user_info
 * wirtten by Simon-Lau
 * created on May 4, 2015
**/
require(dirname(dirname(__FILE__)).'/common.php');
$userInfoDB = Load::table('t_user_info', 'cfg_db_user');

$startTime = strtotime(date("Y-m-d", time())) - 86400;
$endTime = strtotime(date('Y-m-d', time()));

$con = mysql_connect("$ip:$port", "mysql", "mysql") or die("unable to connect!");

mysql_select_db('web_test') or die("Unable to select database!");

$queryDate = date("Ymd", $startTime);
$querySQL = "SELECT COUNT(*) as cnt FROM t_user_info where insert_date = $queryDate";
$result = mysql_fetch_assoc(mysql_query($querySQL));
if($result['cnt'] > 0){
	echo "already have $startTime data{$result['cnt']}";
	$deleteSQL = "delete from t_user_info where insert_date = $queryDate";
	mysql_query($deleteSQL) or die("Error in query...");
}

$insertCount = count($data);
//every time 50 rows of data insert

$k = 0;
$insertLen = 50;
$insertSQL ="";
while ($insertCount > 0){
	$len = $insertCount <= $insertLen ? $insertCount:$insertLen;
	$insertSQL = "INSERT INTO t_user_info (user_id, user_name, user_password, user_age, user_gender,insert_date) VALUES ";
	for($j = 0; $j < $len; $j++){
		$insertSQL .="( '" . $data[$j+$k*$insertLen]['user_id'] ."',";
		$insertSQL .=" '" . $data[$j+$k*$insertLen]['user_name'] . "',";
		$insertSQL .=" '" . $data[$j+$k*$insertLen]['user_password'] . "',";
		$insertSQL .=" '" . $data[$j+$k*$insertLen]['user_age'] . "',";
		$insertSQL .=" '" . $data[$j+$k*$insertLen]['user_gender'] . "',";
		$insertSQL .=" '" . $data[$j+$k*$insertLen]['insert_date'] . "' ),";
	}
	$doneSQL = substr($insertSQL, 0, strlen($insertSQL) - 1);
	mysql_query($doneSQL) or die("Error in query");
	$k++;
	$insertCount -= $insertLen;
}
mysql_close($con);
echo "insert success date: ".date("Y-m-d", startTime) . "number: ".count($data)."\n";