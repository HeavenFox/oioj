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
		OIOJ::$template->display('admin_addproblem.tpl');
	}
	
	public function addedProblem()
	{
		
	}
}
?>