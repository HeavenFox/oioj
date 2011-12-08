<?php
defined('IN_OIOJ') || die('Forbidden');

import('Contest');

require_once MODULE_DIR.'ProblemModule.php';

class ContestProblemModule extends ProblemModule
{
	public $contest;
	
	public function run()
	{
		if (IO::GET('act',false) == 'submit')
		{
			$this->submitSolution();
		}
		else
		{
			$probID = IO::GET('id',0,'intval');
			$contID = IO::GET('cid',0,'intval');
			// Check enrollment status
			$this->contest = new Contest($contID);
			$cu = User::GetCurrent();
			if (!$this->contest->checkEnrollment($cu))
			{
				throw new Exception('You\'re not registered. Please log in (if you have not) and enter from contest homepage.');
			}
			
			$this->loadContest();
			
			if (!OIOJ::$template->isCached('contestproblem.tpl', $probID))
			{
				if (!$this->loadProblem($probID)) {
					throw new Exception('Problem does not exist', 404);
				}
			}
			$this->display($probID);
		}
	}
	
	public function loadContest()
	{
		$this->contest->fetch(array('endTime','duration'),array());
		$started = $this->contest->checkStarted(User::GetCurrent());
		if (!$started)
		{
			$this->contest->userStart(User::GetCurrent(),$started = time());
		}
		OIOJ::$template->assign('user_deadline',min($started + $this->contest->duration,$this->contest->endTime));
		OIOJ::$template->assign('c',$this->contest);
	}
	
	
	public function display($probID)
	{
		OIOJ::$template->display('contestproblem.tpl',$probID);
	}
	
	public function submitSolution()
	{
		$probID = IO::POST('id',0,'intval');
		$contID = IO::POST('cid',0,'intval');
		
		// Check enrollment status
		$contest = Contest::first(array('id','endTime','duration'),array(),$contID);
		$cu = User::GetCurrent();
		if (!$contest->checkEnrollment($cu))
		{
			die(json_encode(array('error' => 'You\'re not registered.')));
		}
		$started = $contest->checkStarted($cu);
		
		if (!$started)
		{
			die(json_encode(array('error' => 'You did not register or start working')));
		}
		
		if (time() > $contest->endTime || time() > ($contest->duration + $started))
		{
			die(json_encode(array('error' => 'Deadline has passed')));
		}
		
		$lang = pathinfo($_FILES['source']['name'], PATHINFO_EXTENSION);
		if (!isset(Problem::$LanguageMap[$lang]))
		{
			$result = array('error' => 'Unsupported Language');
			die(json_encode($result));
		}else
		{
			$lang = Problem::$LanguageMap[$lang];
		}
		
		$code = file_get_contents($_FILES['source']['tmp_name']);
		
		$contest->submitSolution($probID,$cu->id,$code,$lang);
		
		echo json_encode(array('result' => 1));
	}
}
?>