<?php
// Security check
substr($_SERVER['HTTP_USER_AGENT'],0,15) == 'OIOJJudgeServer' || die('Unauthorized access');


require_once 'init.php';

import('OIOJ');

OIOJ::InitDatabase();

import('JudgeRecord');

$record = new JudgeRecord();
$record->token = Config::$Token;

try {
	$record->parseCallback($_POST['general'],$_POST['cases']);
	$record->submit();
} catch (Exception $e)
{
	file_put_contents("log.txt","unauthorized ".strval($e));
	die('Unauthorized access');
}

?>