<?php
import('Contest');
class ContestListModule
{
	public function run()
	{
		$this->showHomePage();
	}
	
	public function showHomePage()
	{
		$paramList = array('id','title','beginTime','endTime','regBegin','regDeadline','duration');
		$compList = array('user' => array('id','username'));
		$commonSuffix = ' LIMIT 0,5';
		$openContests = Contest::find($paramList,$compList,'WHERE `begin_time` > '.time().' AND (`reg_deadline` = 0 OR `reg_deadline` > '.time().') '.$commonSuffix);
		$inProgressContests = Contest::find($paramList,$compList,'WHERE `begin_time` < '.time().' AND `end_time` > '.time().$commonSuffix);
		$readyContests = Contest::find($paramList,$compList,'WHERE `begin_time` > '.time().' AND `reg_deadline` <> 0 AND `reg_deadline` < '.time().$commonSuffix);
		$pastContests = Contest::find($paramList,$compList,'WHERE `end_time` < '.time().$commonSuffix);
		$futureContests = Contest::find($paramList,$compList,'WHERE `reg_begin` > '.time().$commonSuffix);


		OIOJ::$template->assign('open_contests',$openContests);
		OIOJ::$template->assign('inprogress_contests',$inProgressContests);
		OIOJ::$template->assign('ready_contests',$readyContests);
		OIOJ::$template->assign('past_contests',$pastContests);
		OIOJ::$template->assign('future_contests',$futureContests);
		
		OIOJ::$template->display('contestlist-home.tpl');
	}
}
?>