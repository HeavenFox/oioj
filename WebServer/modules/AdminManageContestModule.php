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
		case 'add':
			$this->addContest();
			break;
		case 'edit':
			$this->editContest();
			break;
		}
		
		
	}
	
	private function generateContestForm($record = null)
	{
		$form = new SmartyForm('contest', 'index.php?mod=admin_contest&act=save');
		$form->add(new SF_Hidden('id'));
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
		
		$form->add(new SF_TextField('id','problems','multiple',true));
		
		$form->add(new SF_Select('id','after_submit','label','After Submission','options',array('Save'=>'save','Judge'=>'judge')));
		$form->add(new SF_Checkbox('id','auto_judge','label','Automatically send to judge servers'));
		$form->add(new SF_Number('id','judge_hiatus','data',10));
		$form->add(new SF_Checkbox('id','display_ranking','label','Display Ranking'));
		$form->add(new SF_Checkbox('id','display_preliminary_ranking','label','... Before Judge Finishes'));
		
		$form->add(new SF_TextField('id','criteria','multiple',true));
		$form->add(new SF_TextField('id','criteria-order','multiple',true));
		
		$form->add(new SF_Select('id','display_params','label','Ranking Parameters to Display','multiple',true,'options',array(
			'Total Score' => 'total_score',
			'Num of Correct Submission' => 'num_right',
			'Num of Wrong Submission' => 'num_wrong',
			'Time Used Before Hand-in' => 'duration',
			'Sum of Elapsed Time When Submitted' => 'total_time',
			'Elapsed Time When Last Submitted' => 'max_time'
		)));
		$form->get('display_params')->addValidator(function($val){
			$notpresent = array();
			foreach ($val as $v)
			{
				$present = false;
				foreach (IO::POST('criteria') as $c)
				{
					if (strpos($c, $v) !== null)
					{
						$present = true;
					}
				}
				if (!$present)
				{
					$notpresent[] = $v;
				}
			}
			if (count($notpresent) > 0)
			{
				throw new InputException("Parameter " . implode(', ', $notpresent) . " doesn't appear in criteria.");
			}
		});
		
		if ($record)
		{
			$form->addRecord('contest', $record);
			$form->bind('title','contest');
			$form->bind('description','contest');
			$form->bind('starttime','contest','beginTime');
			$form->bind('endtime','contest','endTime');
			$form->bind('duration','contest','duration');
			$form->bind('publicity','contest');
			$form->bind('regstart','contest','regBegin');
			$form->bind('regend','contest','regDeadline');
		}
		
		return $form;
	}
	
	private $basicSettingsFromAssoc = array('early_handin','after_submit','display_problem_title_before_start','display_preliminary_ranking','display_ranking');
	
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
	
	private function gatherOptionsToForm(Contest $contest, SmartyForm $form)
	{
		foreach ($this->basicSettingsFromAssoc as $v)
		{
			$form->get($v)->data = $contest->getOption($v);
		}
		// Ranking criteria
		$criteria_array = explode(';', $contest->getOption('ranking_criteria'));
		
		$form->get('display_params')->data = explode(';', $contest->getOption('ranking_display_params'));
		
	}
	
	private function saveOptionsToContest(SmartyForm $form, Contest $contest)
	{
		foreach ($this->basicSettingsFromAssoc as $v)
		{
			$contest->setOption($v,$form->get($v)->data);
		}
		// Ranking Criteria
		$criteria = $form->get('criteria')->data;
		$criteria_order = $form->get('criteria-order')->data;
		foreach ($criteria as $k=>$v)
		{
			$criteria[$k] = $criteria_order[$k].$v;
		}
		$contest->setOption('ranking_criteria',implode(';',$criteria));
		$contest->setOption('ranking_display_params',implode(';',$form->get('display_params')->data));
	}
	
	public function saveContest()
	{
		$form = $this->generateContestForm($c);
		
		$form->gatherFromPOST();
		
		$form->validate();
		
		if (!count($criteria))
		{
			throw new Exception('You must specify at least one ranking criterion');
		}
		
		if (!$form->valid)
		{
			$this->showForm($form);
		}
		
		$contestID = intval($form->get('id')->data);
		
		if ($contestID)
		{
			$c = new Contest($contestID);
		}
		else
		{
			$c = new Contest();
		}
		
		if (!$contestID)
		{
			$c->user = User::GetCurrent();
			$c->status = Contest::STATUS_WAITING;
		}
		
		$c->submit();
		
		$this->saveOptionsToContest($form, $c);
		
		
		if ($contestID)
		{
			Database::Get()->exec('DELETE FROM `oj_contest_problems` WHERE `cid` = '.$contestID);
		}
		
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
		$this->showForm($this->generateContestForm());
	}
	
	public function editContest()
	{
		$c = Contest::first(array('id','title','description','beginTime','endTime','regBegin','regDeadline','duration','publicity'), 'WHERE `id` = '.IO::GET('id',0,'intval'));
		$form = $this->generateContestForm($c);
		$form->gatherFromRecord();
		$this->gatherOptionsToForm($c, $form);
		$this->showForm($form);
	}
	
	private function showForm($form)
	{
		OIOJ::$template->assign('contestform',$form);
		OIOJ::$template->display('admin_editcontest.tpl');
	}
}
?>