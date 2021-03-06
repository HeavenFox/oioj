<?php
defined('IN_OIOJ') || die('Forbidden');

import('Contest');
class ContestListModule
{
	public function run()
	{
		User::GetCurrent()->assertNotUnable('view_contest');
		$this->showHomePage();
	}
	
	public function showHomePage()
	{
		OIOJ::AddBreadcrumb('Arena');
		$paramList = array('id','title','beginTime','endTime','regBegin','regDeadline','duration','user' => array('id','username'));
		$commonSuffix = ' LIMIT 0,5';
		$openContests = Contest::find($paramList,'WHERE `publicity` > 0 AND `begin_time` > '.time().' AND (`reg_deadline` IS NULL OR `reg_deadline` > '.time().') '.$commonSuffix);
		$inProgressContests = Contest::find($paramList,'WHERE `publicity` > 0 AND `begin_time` < '.time().' AND `end_time` > '.time().$commonSuffix);
		$readyContests = Contest::find($paramList,'WHERE `publicity` > 0 AND `begin_time` > '.time().' AND `reg_deadline` IS NOT NULL AND `reg_deadline` < '.time().$commonSuffix);
		$pastContests = Contest::find($paramList,'WHERE `publicity` > 0 AND `end_time` < '.time().$commonSuffix);
		$futureContests = Contest::find($paramList,'WHERE `publicity` > 0 AND `reg_begin` > '.time().$commonSuffix);


		OIOJ::$template->assign('open_contests',$openContests);
		OIOJ::$template->assign('inprogress_contests',$inProgressContests);
		OIOJ::$template->assign('ready_contests',$readyContests);
		OIOJ::$template->assign('past_contests',$pastContests);
		OIOJ::$template->assign('future_contests',$futureContests);
		
		OIOJ::$template->display('contestlist-home.tpl');
	}
}
?>