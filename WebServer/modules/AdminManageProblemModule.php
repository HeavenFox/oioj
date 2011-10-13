<?php
class AdminManageProblemModule
{
	
	/*
	 * Check for administration privilege
	 */
	public function authenticate()
	{
		
	}
	
	public function run()
	{
		switch (IO::GET('act'))
		{
		case 'add':
			if (IO::GET('submit'))
			{
				$this->addProblemSubmit();
			}
			else
			{
				OIOJ::$template->display('admin_addproblem.tpl');
			}
			
			break;
		}
		
	}
	
	public function addProblemSubmit()
	{
		var_dump($_POST);
	}
}
?>