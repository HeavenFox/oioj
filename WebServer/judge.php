<?php
require_once 'init.php';
import('JudgeRecord');
import('JudgeServer');

$record = new JudgeRecord;
$record->lang = $_POST['lang'];
$record->problemID = intval($_POST['pid']);
$record->setSubmission($_POST['code']);

$servers = JudgeServer::GetAvailableServers();

$success = false;

while ($server = array_shift($servers))
{
	if ($server->dispatch($record))
	{
		$success = true;
		$server->addWorkload();
		$record->status = JudgeServer::STATUS_DISPATCHED;
		break;
	}
}

$record->submit();

?>