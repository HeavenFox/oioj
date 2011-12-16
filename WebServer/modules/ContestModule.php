<?php
import('Contest');
import('Problem');

class ContestModule
{
	public $contestId;
	public $registered;
	
	public function __construct()
	{
		$this->contestId = IO::GET('id',0,'intval');
		$c = new Contest($this->contestId);
		$this->registered = $c->checkEnrollment(User::GetCurrent());
	}
	
	public function run()
	{
		switch (IO::GET('act',null))
		{
		case 'register':
			$this->register();
			break;
		default:
			$this->showProfile();
		}
	}
	
	public function register()
	{
		if (User::GetCurrent()->id == 0)
		{
			throw new Exception('Please log in before you register for a contest');
		}
		$contest_id = IO::GET('id',0,'intval');
		if ($this->registered)
		{
			throw new Exception('You have registered for this contest');
		}
		$db = Database::Get();
		
		$db->exec('INSERT INTO `oj_contest_register` (cid,uid) VALUES ('.$this->contestId.','.User::GetCurrent()->id.')');
		
		OIOJ::$template->assign('global_message','You have registered for this contest');
		
		IO::SetSession('contest-registered-'.$contest_id,true);
		
		$this->showProfile();
	}
	
	public function showProfile()
	{
		$contest = Contest::first(array('id','title','description','regBegin','regDeadline','beginTime','endTime','duration','status'),array('user'=>array('username')),'WHERE `oj_contests`.`id`='.$this->contestId);
		
		if (intval($contest->getOption('display_problem_title_before_start')) || $contest->status != Contest::STATUS_WAITING)
		{
			$contest->getComposite(array('problems' => array('id','title','input','output')));
		}
		if (intval($contest->getOption('display_preliminary_ranking')) || $contest->status == Contest::STATUS_JUDGED)
		{
			
		}
		else if ($contest->status != Contest::STATUS_JUDGED && intval($contest->getOption('display_participants_before_end')))
		{
			$contest->getComposite(array('participants' => array('id','username')));
		}
		
		OIOJ::$template->assign('registered',$this->registered);
		OIOJ::$template->assign('started',$contest->checkStarted(User::GetCurrent()));
		
		OIOJ::$template->assign('c',$contest);
		OIOJ::$template->assign('ranking',array_slice($contest->generateRanking(),0,10));
		
		OIOJ::$template->assign('ranking_display_params',array_flip(explode(';',$contest->getOption('ranking_display_params'))));
		OIOJ::$template->display('contest.tpl');
	}
	
	public function displayRanking()
	{
		
	}
}
?>