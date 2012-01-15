<?php
defined('IN_OIOJ') || die('Forbidden');

import('Problem');
import('Cronjob');
class AdminManageProblemModule
{
	public function run()
	{
		$user = User::GetCurrent();
		if (!($user->ableTo('add_problem') || ($user->ableTo('admin_cp') && !$user->unableTo('add_problem'))))
		{
			throw new PermissionException();
		}
		
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
		try
		{
			Settings::Flush();
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
			$prob->listing = IO::POST('listing',0,function($data){return 1;});
			
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
			echo "Checking if archive file is legal...<br />\n";
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
			
			$newloc = Settings::Get('tmp_dir').'cases/'.md5(rand());
			
			move_uploaded_file($_FILES['archive']['tmp_name'],$newloc);
			
			$prob->archiveLocation = $newloc;
			
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
			
			$prob->dispatched = 0;
			
			$prob->add();
			
			$db = Database::Get();
			
			echo 'Adding problem to dispatch queue<br />';
			
			$servers = JudgeServer::find(array('id'));
			
			if ($servers)
			{
				$insQuery = 'INSERT INTO `oj_probdist_queue` (`pid`,`server`,`file`) VALUES ';
			
				$first = true;
				foreach ($servers as $server)
				{
					if ($first)
					{
						$first = false;
					}else
					{
						$insQuery .= ',';
					}
				
					$insQuery .= "({$prob->id},{$server->id},:archive)";
				}
				
				$stmt = $db->prepare($insQuery);
				$stmt->bindParam('archive',$newloc);
				$stmt->execute();
			}
			
			Cronjob::AddJob('ProblemDistribution','dispatch',array(), 0, 3);
			
			echo 'done. Problem ID: '.$prob->id;
			echo '<script type="text/javascript">parent.resetForm();</script>';
		}catch(Exception $e)
		{
			echo $e;
		}
	}
}
?>
