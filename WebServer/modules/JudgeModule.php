<?php
IN_OIOJ || die('Forbidden');

import('JudgeRecord');
import('JudgeServer');

class JudgeModule
{
	public function run()
	{
		OIOJ::InitDatabase();
		
		$record = new JudgeRecord;
		$record->lang = $_POST['lang'];
		$record->problemID = intval($_POST['pid']);
		$record->code = ($_POST['code']);
		
		$record->token = Config::$Token;
		
		$servers = JudgeServer::GetAvailableServers();
		
		$db = Database::Get();
		
		$record->submit();
		
		while ($server = array_shift($servers))
		{
			if ($server->dispatch($record))
			{
				$server->addWorkload();
				$record->status = JudgeRecord::STATUS_DISPATCHED;
				$record->serverID = $server->id;
				break;
			}
		}
		
		
		$record->submit();
	}
}