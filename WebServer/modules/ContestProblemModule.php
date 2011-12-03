<?php
defined('IN_OIOJ') || die('Forbidden');

import('Contest');

require_once MODULE_DIR.'ProblemModule.php';

class ContestProblemModule extends ProblemModule
{
	public $contest;
	
	public function run()
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
	
	public function loadContest()
	{
		$this->contest->fetch(array('endTime'),array());
		OIOJ::$template->assign('user_deadline',min(IO::Session('contest-deadline',2147483647),$this->contest->endTime));
		OIOJ::$template->assign('c',$this->contest);
	}
	
	
	public function display($probID)
	{
		OIOJ::$template->display('contestproblem.tpl',$probID);
	}
	
	public function submitSolution()
	{
		$lang = pathinfo($_FILES['source']['name'], PATHINFO_EXTENSION);
		if (!isset(Problem::$LanguageMap[$lang]))
		{
			$result = array('error' => 'Unsupported Language');
			echo json_encode($result);
		}else
		{
			$lang = Problem::$LanguageMap[$lang];
		}
		
		$code = file_get_contents($_FILES['source']['name']);
		
		$this->contest->submitSolution($this->id,User::GetCurrent()->id,$code,$lang);
		
		echo json_encode(array('result' => 1));
	}
}
?>