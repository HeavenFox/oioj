<?php
class HomeModule
{
	public function run()
	{
		if (User::GetCurrent()->id == 0)
		{
			// Prepare SmartyForm
			import('SmartyForm');
			$loginForm = new SmartyForm('login','index.php?mod=user&act=login');
			
			$loginForm->add(new SF_TextField('id','username','label','Username'));
			$loginForm->add(new SF_Password('id','password','label','Password'));
			$loginForm->add(new SF_Checkbox('id','remember','label','Remember me?'));
			
			$mod = new UserModule;
			OIOJ::$template->assign('sf_register',$mod->generateRegisterForm());
			
			OIOJ::$template->assign('sf_login', $loginForm);
		}
		OIOJ::$template->display('index.tpl');
	}
}
?>