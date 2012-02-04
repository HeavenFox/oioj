<?php
defined('IN_OIOJ') || die('Forbidden');

import('Contest');
import('Problem');
import('Cronjob');
class AdminManageContestModule
{
	public function run()
	{
		User::GetCurrent()->assertAble('add_contest');
		
		switch (IO::GET('act'))
		{
		case 'getproblemtitle':
			$this->ajaxGetProblemTitle();
			break;
		case 'save':
			$this->saveContest();
			break;
		default:
			$this->addContest();
		}
		
		
	}
	
	private function getProblemTitle($id)
	{
		$p = Problem::first(array('title'),$id);
		if ($p)
		return $p->title;
		else
			return false;
	}
	
	public function ajaxGetProblemTitle()
	{
		$id = IO::GET('id',0,'intval');
		if ($t = $this->getProblemTitle($id))
		{
			echo json_encode(array('title' => $t));
		}else
		{
			echo json_encode(array('error' => 'Invalid Problem ID'));
		}
	}
	
	private function getTimestamp($name)
	{
		return strtotime(IO::POST($name.'-date').' '.IO::POST($name.'-h').':'.IO::POST($name.'-m').':'.IO::POST($name.'-s'));
	}
	
	public function generateForm()
	{
		$form = new SmartyForm();
	}
	
	public function saveContest()
	{
		// Ranking Criteria
		$criteria = IO::POST('criteria');
		$criteria_order = IO::POST('criteria-order');
		
		if (!count($criteria))
		{
			throw new Exception('You must specify at least one ranking criterion');
		}
		
		foreach ($criteria as $k=>$v)
		{
			$criteria[$k] = $criteria_order[$k].$v;
		}
		
		
		$c = new Contest();
		
		$c->title = IO::POST('title');
		$c->description = IO::POST('description');
		
		$c->user = User::GetCurrent();
		
		$c->beginTime = $this->getTimestamp('starttime');
		$c->endTime = $this->getTimestamp('endtime');
		$c->duration = intval(IO::POST('duration-h'))*3600+intval(IO::POST('duration-m'))*60+intval(IO::POST('duration-s'));
		if (strlen(IO::POST('regstart-date')))
		{
			$c->regBegin = $this->getTimestamp('regstart');
		}
		$c->publicity = IO::POST('publicity',0,'intval');
		$c->status = Contest::STATUS_WAITING;
		if (strlen(IO::POST('regend-date')))
		{
			$c->regDeadline = $this->getTimestamp('regend');
		}
		
		$c->submit();
		
		$cbCallback = function($val)
		{
			return 1;
		};
		
		$c->addOption('early_handin',IO::POST('early_handin',0,$cbCallback));
		$c->addOption('after_submit',IO::POST('after_submit','save'));
		$c->addOption('display_problem_title_before_start',IO::POST('display_problem_title_before_start',0,$cbCallback));
		$c->addOption('display_preliminary_ranking',IO::POST('display_preliminary_ranking',0,$cbCallback));
		$c->addOption('display_ranking',IO::POST('display_ranking',0,$cbCallback));
		
		
		
		$c->addOption('ranking_criteria',implode(';',$criteria));
		$c->addOption('ranking_display_params',implode(';',IO::POST('display_params')));
		
		
		$addAssocQuery = 'INSERT INTO `oj_contest_problems` (`cid`,`pid`) VALUES ';
		$first = true;
		foreach (IO::POST('problems') as $v)
		{
			if ($first)
			{
				$first=false;
			}else
			{
				$addAssocQuery.=',';
			}
			$addAssocQuery.= '('.$c->id.','.intval($v).')';
		}
		
		$db = Database::Get();
		$db->exec($addAssocQuery);
		
		// Add Cron Jobs as Required
		if (IO::POST('auto_start',false,$cbCallback))
		{
			Cronjob::AddJob('Contest','start',array(),$c->beginTime,0,$c->id);
		}
		
		Cronjob::AddJob('Contest','end',array(),$c->endTime,0,$c->id);
		
		if (IO::POST('auto_judge',false,$cbCallback))
		{
			Cronjob::AddJob('Contest','judge',array(),$c->endTime + 60*IO::POST('judge-hiatus',10,'intval'),1,$c->id);
		}
		
		OIOJ::Redirect('The Contest Has Been Saved');
	}
	
	public function addContest()
	{
		OIOJ::$template->display('admin_editcontest.tpl');
	}
}
?>