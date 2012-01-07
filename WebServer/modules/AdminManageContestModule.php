<?php
class AdminManageContestModule
{
	public function run()
	{
		switch (IO::GET('act'))
		{
		case 'cp':
			
		default:
			$this->addContest();
		}
		
		
	}
	
	public function addContest()
	{
		if (IO::GET('submit'))
		{
			
		}
		else
		{
			OIOJ::$template->display('admin_editcontest.tpl');
		}
	}
}
?>