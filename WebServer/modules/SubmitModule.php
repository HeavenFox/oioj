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
		
		$record = new JudgeRecord;
		
		$record->code = file_get_contents($_FILES['source']['tmp_name']);
		
		unlink($_FILES['source']['tmp_name']);
		
		$problemID = 0;
		
		if (($problemID = IO::GET('id',0,'intval')) <= 0)
		{
			preg_match('/[0-9]+/',$_FILES['source']['name'],$matches);
			if (!isset($matches[0]))
			{
				die(json_encode(array('error' => 'You did not indicate problem no.')));
			}
			$problemID = intval($matches[0]);
		}
		
		$uid = IO::Session('uid');
		$lang = strtolower(pathinfo($_FILES['source']['name'], PATHINFO_EXTENSION));
		
		$map = Problem::$LanguageMap;
		
		if (!isset($map[$lang]))
		{
			die(json_encode(array('error' => 'Unsupported language. Check if file extension is correct')));
		}
		
		$lang = $map[$lang];
		
		$record->setTokens();
		
		$record->lang = $lang;
		import('User');
		$record->user = User::GetCurrent();
		$record->problem = new Problem($problemID);
		$record->submit();
		
		
		$db = Database::Get();
		
		$record->dispatch();
		
		echo json_encode(array('record_id' => $record->id, 'server_name' => $server->name));
	}
	
	public function showForm()
	{
		OIOJ::$template->display('submit.tpl');
	}
}
?>