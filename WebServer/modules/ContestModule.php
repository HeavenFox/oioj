<?php
import('Contest');
import('Problem');
class ContestModule
{
	public function run()
	{
		$contest = Contest::first(array('id','title','description','regBegin','regDeadline'),array('user'=>array('username')),'WHERE `oj_contests`.`id`='.IO::GET('id','intval'));
		$contest->getComposite(array('problems'=>array('id','title')));
		OIOJ::$template->assign('c',$contest);
		OIOJ::$template->display('contest.tpl');
	}
}
?>