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
			OIOJ::InitDatabase();
			$obj = Problem::first(array('title','body'),null,'WHERE `id` = '.$probID);
			if ($obj) {
				OIOJ::$template->assign('pid', $probID);
				OIOJ::$template->assign('problem', $obj);
			} else {
				throw new Exception('Problem does not exist', 404);
			}
		}
		
		OIOJ::$template->display('problem.tpl',$probID);
	}
}
?>