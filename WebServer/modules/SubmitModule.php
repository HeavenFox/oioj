<?php
import('JudgeRecord');
import('JudgeServer');

class SubmitModule
{
	public function run()
	{
		if (!User::GetCurrent()->ableTo('submit_solution'))
		{
			throw new PermissionException();
		}
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
		
		if (($problemID = IO::REQUEST('id',0,'intval')) <= 0)
		{
			preg_match('/[0-9]+/',$_FILES['source']['name'],$matches);
			if (!isset($matches[0]))
			{
				throw new Exception('You did not indicate problem no.');
			}
			$problemID = intval($matches[0]);
		}
		
		$uid = IO::Session('uid');
		$lang = strtolower(pathinfo($_FILES['source']['name'], PATHINFO_EXTENSION));
		
		$map = Problem::$LanguageMap;
		
		if (!isset($map[$lang]))
		{
			throw new Exception('Unsupported language. Check if file extension is correct');
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
		
		if (IO::GET('ajax'))
		{
			echo json_encode(array('record_id' => $record->id, 'server_name' => $record->server ? $record->server->name : 'To be determined'));
		}
		else
		{
			OIOJ::Redirect('Your submission has been received. Now redirecting to status monitoring page...','index.php?mod=records&id='.$record->id);
		}
	}
	
	public function showForm()
	{
		OIOJ::$template->display('submit.tpl');
	}
}
?>