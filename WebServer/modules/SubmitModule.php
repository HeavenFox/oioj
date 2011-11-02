<?php
import('JudgeRecord');
import('JudgeServer');

class SubmitModule
{
	public function run()
	{
		if (IO::GET('solution'))
		{
			$this->submitSolution();
		}
		else
		{
			$this->showForm();
		}
	}
	
	public function submitSolution()
	{
		error_reporting(0);
		OIOJ::InitDatabase();
		
		$record = new JudgeRecord;
		
		$record->code = (file_get_contents($_FILES['source']['tmp_name']));
		
		unlink($_FILES['source']['tmp_name']);
		
		preg_match('/[0-9]+/',$_FILES['source']['name'],$matches);
		if (!isset($matches[0]))
		{
			die(json_encode(array('error' => 'You did not indicate problem no.')));
		
		}
		$problemID = intval($matches[0]);
		$uid = IO::Session('uid');
		$lang = pathinfo($_FILES['source']['name'], PATHINFO_EXTENSION);
		
		$map = array(
			'c' => 'c',
			'cpp' => 'cpp',
			'cc' => 'cpp',
			'cxx' => 'cpp',
			'pas' => 'pas'
		);
		
		if (!isset($map[$lang]))
		{
			die(json_encode(array('error' => 'Unsupported language. Check if file extension is correct')));
		}
		
		$lang = $map[$lang];
		
		$record->token = Config::$Token;
		$record->lang = $lang;
		import('User');
		$record->user = User::GetCurrent();
		$record->pid = $problemID;
		$record->submit();
		
		$servers = JudgeServer::GetAvailableServers();
		
		$db = Database::Get();
		
		
		$server = null;
		
		while ($server = array_shift($servers))
		{
			if ($server->dispatch($record))
			{
				$server->addWorkload();
				$record->status = JudgeRecord::STATUS_DISPATCHED;
				$record->server = $server;
				break;
			}
		}
		
		$record->submit();
		
		echo json_encode(array('record_id' => $record->id, 'server_name' => $server->name));
	}
	
	public function showForm()
	{
		OIOJ::$template->display('submit.tpl');
	}
}
?>