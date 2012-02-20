<?php
defined('IN_OIOJ') || die('Forbidden');

import('Contest');
import('Problem');
import('Cronjob');
import('SmartyForm');
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
	
	private function generateContestForm($record = null)
	{
		$form = new SmartyForm('contest', 'index.php?mod=admin_contest&act=save');
		$form->add(new SF_TextField('id','title','label','Title'));
		$form->add(new SF_TextArea('id','description','label','Description'));
		$form->add(new SF_DateTime('id','starttime','label','Scheduled Start Time'));
		$form->add(new SF_DateTime('id','endtime','label','Scheduled End Time'));
		$form->add(new SF_Duration('id','duration','label','Duration'));
		$form->add(new SF_Checkbox('id','early_handin','label','Support Early Hand-in'));
		$form->add(new SF_Checkbox('id','auto_start','label','Automatically start at scheduled time'));
		
		$form->add(new SF_Select('id','publicity','label','Publicity Level','options',array('Unlisted: contest will be invisible to ordinary user'=>'0','Internal: contest is visible, but not available for register'=>'1','Register: users need to register beforehand' => '2','Auto: automatically register user once begin working'=>'3')));
		$form->add(new SF_DateTime('id','regstart','label','Registration Begins'));
		$form->add(new SF_DateTime('id','regend','label','Registration Ends'));
		$form->add(new SF_Checkbox('id','display_problem_title_before_start','label','Display titles before contest starts'));
		// Problems: not supported by SmartyForm
		
		$form->add(new SF_Checkbox('id','auto_judge','label','Automatically send to judge servers'));
		$form->add(new SF_Number('id','judge_hiatus','data',10));
		$form->add(new SF_Checkbox('id','display_ranking','label','Display Ranking'));
		$form->add(new SF_Checkbox('id','display_preliminary_ranking','label','... Before Judge Finishes'));
		// Ranking Criteria: ditto
		$form->add(new SF_Select('id','display_params','label','Ranking Parameters to Display','multiple',true,'options',array(
			'Total Score' => 'total_score',
			'Num of Correct Submission' => 'num_right',
			'Num of Wrong Submission' => 'num_wrong',
			'Time Used Before Hand-in' => 'duration',
			'Sum of Elapsed Time When Submitted' => 'total_time',
			'Elapsed Time When Last Submitted' => 'max_time'
		)));
		
		if ($record)
		{
			$form->addRecord('contest', $record);
			$form->bind('title','contest');
			$form->bind('description','contest');
			$form->bind('starttime','contest','beginTime');
			$form->bind('endtime','contest','endTime');
			$form->bind('publicity','contest');
			$form->bind('regstart','contest','regBegin');
			$form->bind('regend','contest','regDeadline');
		}
		
		return $form;
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
		
		
		$c->user = User::GetCurrent();
		
		$c->duration = intval(IO::POST('duration-h'))*3600+intval(IO::POST('duration-m'))*60+intval(IO::POST('duration-s'));
		
		$c->status = Contest::STATUS_WAITING;
		
		
		$c->submit();
		
		$cbCallback = function($val)
		{
			return 1;
		};
		
		$c->setOption('early_handin',IO::POST('early_handin',0,$cbCallback));
		$c->setOption('after_submit',IO::POST('after_submit','save'));
		$c->setOption('display_problem_title_before_start',IO::POST('display_problem_title_before_start',0,$cbCallback));
		$c->setOption('display_preliminary_ranking',IO::POST('display_preliminary_ranking',0,$cbCallback));
		$c->setOption('display_ranking',IO::POST('display_ranking',0,$cbCallback));
		
		
		
		$c->setOption('ranking_criteria',implode(';',$criteria));
		$c->setOption('ranking_display_params',implode(';',IO::POST('display_params')));
		
		
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
		OIOJ::$template->assign('contestform',$this->generateContestForm());
		OIOJ::$template->display('admin_editcontest.tpl');
	}
	
	public function editContest()
	{
		$contest = 
		OIOJ::$template->assign('contestform',$this->generateContestForm());
		OIOJ::$template->display('admin_editcontest.tpl');
	}
}
?>