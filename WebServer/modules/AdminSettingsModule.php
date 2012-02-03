<?php
import('SettingsForm');
class AdminSettings
{
	public function run()
	{
		$form = new SettingsForm('settings','index.php?mod=admin_settings&act=submit');
		$form->add(SF_ServerBrowser('id','tmp_dir','label','Temp Dir'));
		
		
	}
}
?>