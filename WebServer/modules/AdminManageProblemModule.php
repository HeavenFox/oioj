<?php
import('Problem');
class AdminManageProblemModule
{
	
	/*
	 * Check for administration privilege
	 */
	public function authenticate()
	{
		
	}
	
	public function run()
	{
		switch (IO::GET('act'))
		{
		case 'add':
			if (IO::GET('submit'))
			{
				$this->addProblemSubmit();
			}
			else
			{
				OIOJ::$template->display('admin_addproblem.tpl');
			}
			
			break;
		}
		
	}
	
	public function addProblemSubmit()
	{
		// Check permission
		if (!User::GetCurrent()->ableTo('add_problem'))
		{
			throw new Exception('denied');
		}
		
		
		$prob = new Problem();
		$prob->user = User::GetCurrent();
		$prob->title = IO::POST('title');
		$prob->body = IO::POST('body');
		$prob->type = IO::POST('type');
		
		$prob->input = IO::POST('input_file');
		$prob->output = IO::POST('output_file');
		$prob->compare = IO::POST('comp_method');
		if ($prob->compare == 'special')
		{
			$prob->compare = IO::POST('special_judge');
		}
		$inputs = IO::POST('case-in');
		$outputs = IO::POST('case-out');
		$tls = IO::POST('case-tl');
		$mls = IO::POST('case-ml');
		$scores = IO::POST('case-score');
		
		// Check if archive valid
		$zip = new ZipArchive();
		if (is_uploaded_file($_FILES['archive']['tmp_name']))
		{
			$zip->open($_FILES['archive']['tmp_name']);
			foreach ($scores as $k => $v)
			{
				if ($zip->locateName($inputs[$k],ZIPARCHIVE::FL_NODIR) === false || $zip->locateName($outputs[$k],ZIPARCHIVE::FL_NODIR) === false)
				{
					throw new Exception('wrong archive');
				}
			}
			$zip->close();
		}else{
			throw new Exception('error reading archive');
		}
		foreach ($scores as $k => $v)
		{
			$c = new TestCase();
			$c->cid = $k+1;
			$c->input = $inputs[$k];
			$c->answer = $outputs[$k];
			$c->timelimit = $tls[$k];
			$c->memorylimit = $mls[$k];
			$c->score = $v;
			$c->problem = $prob;
			
			$prob->testCases[] = $c;
		}
		
		$prob->add();
		foreach ($prob->testCases as $v)
		{
			$v->add();
		}
		echo 'done';
	}
}
?>