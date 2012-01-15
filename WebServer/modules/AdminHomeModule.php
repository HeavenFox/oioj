<?php
defined('IN_OIOJ') || die('Forbidden');
class AdminHomeModule
{
	public function run()
	{
		if (!User::GetCurrent()->ableTo('admin_cp'))
		{
			throw new PermissionException();
		}
		OIOJ::$template->display('admin_home.tpl');
	}

}
?>