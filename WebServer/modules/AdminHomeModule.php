<?php
defined('IN_OIOJ') || die('Forbidden');
class AdminHomeModule
{
	public function run()
	{
		User::GetCurrent()->assertAble('admin_cp');
		OIOJ::$template->display('admin_home.tpl');
	}

}
?>