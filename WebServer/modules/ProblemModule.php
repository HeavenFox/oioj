<?php
defined('IN_OIOJ') || die('Forbidden');

import('Problem');

class ProblemModule
{
	protected $problem;
	public function run()
	{
		$probID = IO::GET('id',0,'intval');
		
		if (!OIOJ::$template->isCached('problem.tpl', $probID))
		{
			if (!($obj = $this->loadProblem($probID)) || $obj->listing == 0) {
				throw new Exception('Problem does not exist', 404);
			}
		}
		$this->display($probID);
	}
	
	public function loadProblem($id)
	{
		$this->problem = Problem::first(array('id','title','body','accepted','submission','source','listing'),array('user' => array('username')),$id);
		if (!$this->problem) return false;
		OIOJ::$template->assign('pid', $id);
		OIOJ::$template->assign('problem', $this->problem);
		return $this->problem;
	}
	
	public function display($probID)
	{
		OIOJ::AddBreadcrumb(array('Problems' => 'index.php?mod=problemlist', $this->problem->title => ''));
		
		OIOJ::$template->display('problem.tpl',$probID);
	}
}
?>