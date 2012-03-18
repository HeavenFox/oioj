<?php
// Security check
substr($_SERVER['HTTP_USER_AGENT'],0,15) == 'OIOJJudgeServer' || die('Unauthorized access');

require_once 'init.php';

import('OIOJ');

import('Settings');

OIOJ::InitDatabase();

import('JudgeRecord');

$record = new JudgeRecord();

try {
	$record->parseCallback($_POST['data']);
} catch (Exception $e)
{
	die('Unauthorized access');
}

JudgeRecord::PopWaitlist();

?>