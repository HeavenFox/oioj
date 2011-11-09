<?php
defined('IN_OIOJ') || die('Forbidden');

require_once MODULE_DIR.'ProblemModule.php';

class ContestProblemModule extends ProblemModule
{
	public function run()
	{
		$probID = IO::GET('id',0,'intval');
		
		// Check enrollment status
		$cu = User::GetCurrent();
		if ($cu->id == 0 || !IO::Session('contest-reg-'.$cu->id))
		{
			throw new Exception('You\'re not registered. If you are, please log in (if your session expired) and enter from contest homepage.')
		}
		
		$this->loadContest();
		
		if (!OIOJ::$template->isCached('problem.tpl', $probID))
		{
			if (!$this->loadProblem($probID)) {
				throw new Exception('Problem does not exist', 404);
			}
		}
		
	}
	
	public function loadContest($id)
	{
		OIOJ::$template->assign('user_deadline',IO::Session('contest-deadline'));
		
	}
	
	
	public function display($probID)
	{
		OIOJ::$template->display('contestproblem.tpl',$probID);
	}
}
?>