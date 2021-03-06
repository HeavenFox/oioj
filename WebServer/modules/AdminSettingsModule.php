<?php
import('SettingsForm');
class AdminSettingsModule
{
	public function generateForm()
	{
		$form = new SettingsForm('settings','index.php?mod=admin_settings&act=save');
		$form->add(new SF_ServerBrowser('id','tmp_dir','label','Temporary Dir','object','directory'));
		$form->add(new SF_TextField('id','token','label','Token'));
		$form->add(new SF_TextField('id','backup_token','label','Backup Token'));
		$form->add(new SF_ServerBrowser('id','local_judgeserver_data_dir','label','Local Judge Server Data Folder','object','directory'));
		
		$form->add(new SF_ServerBrowser('id','data_archive_dir','label','Data Archive Directory','object','directory'));
		$form->add(new SF_TextField('id','recaptcha_public','label','reCAPTCHA Public'));
		$form->add(new SF_TextField('id','recaptcha_private','label','reCAPTCHA Private'));
		$form->add(new SF_TimeZone('id','default_timezone','label','Default Time Zone'));
		
		return $form;
	}
	
	public function run()
	{
		User::GetCurrent()->assertAble('manage_settings');
		$form = $this->generateForm();
		if (IO::GET('act') == 'save')
		{
			$form->gatherFromPOST();
			$form->saveToSettings();
			OIOJ::GlobalMessage('Saved Successfully');
		}
		else
		{
			$form->gatherFromSettings();
		}
		
		OIOJ::$template->assign('sf_settings',$form);
		OIOJ::$template->display('admin_settings.tpl');
	}
}
?>