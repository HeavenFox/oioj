<?php
class AdminManageContestModule
{
	public function run()
	{
		switch (IO::GET('act'))
		{
		}
		$this->addContest();
		
	}
	
	public function addContest()
	{
		OIOJ::$template->display('admin_editcontest.tpl');
	}
}
?>