<?php
ob_start();
require_once 'init.php';

import('OIOJ');

OIOJ::PrepareDatabase();

import('JudgeRecord');

$record = new JudgeRecord();
var_dump($_POST['general']);
var_dump($_POST['cases']);
$record->parseCallback($_POST['general'],$_POST['cases']);
$record->submit();
file_put_contents('test.txt',ob_get_contents());
?>