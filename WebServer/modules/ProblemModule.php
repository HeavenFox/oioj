<?php
defined('IN_OIOJ') || die('Forbidden');

import('Problem');

class ProblemModule
{
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
		$obj = Problem::first(array('id','title','body','accepted','submission','source','listing'),array('user' => array('username')),$id);
		if (!$obj) return false;
		OIOJ::$template->assign('pid', $id);
		OIOJ::$template->assign('problem', $obj);
		return $obj;
	}
	
	public function display($probID)
	{
		OIOJ::$template->display('problem.tpl',$probID);
	}
}
?>