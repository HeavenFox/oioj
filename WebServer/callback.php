<?php
// Security check
substr($_SERVER['HTTP_USER_AGENT'],0,15) == 'OIOJJudgeServer' || die('Unauthorized access');

require_once 'init.php';

import('OIOJ');

import('Settings');

OIOJ::InitDatabase();

import('JudgeRecord');

try {
	$record->parseCallback($_POST['general'],$_POST['cases']);
	$record->submit();
} catch (Exception $e)
{
	die('Unauthorized access');
}

?>