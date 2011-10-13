<?php
IN_OIOJ || die('Forbidden');


import('Invitation');
import('User');
class UserModule
{
	public function autoload()
	{
		// Begin session
		session_start();
		
		
	}
	
	public function run()
	{
		switch (IO::GET('act'))
		{
		case 'login':
			$this->doLogin();
			break;
		case 'register_submit':
			$this->doRegistered();
			break;
		case 'getcaptcha':
			$this->getCAPTCHA();
			break;
		}
	}
	
	public function doLogin()
	{
		
	}
	
	public function doRegisterSubmit()
	{
		// 
		
		
		$suppliedCode = IO::POST('invitation',null,function($code){return preg_replace('/[^a-zA-Z0-9]/','',$code);});
		
		$invit = Invitation::first(array('id'),null,'WHERE `code` = \''.$suppliedCode.'\'');
		
		if (!$invit)
		{
			throw new Exception('Sorry, invitation code invalid');
		}
		
		$user = new User();
		
		
		
		$invit->user = $user;
		$invit->update();
		
		$user->create();
		
		$user->createSession();
	}
	
	public function getCAPTCHA()
	{
		require_once LIB_DIR . 'recaptchalib.php';
		echo recaptcha_get_html(Config::$CAPTCHA_Public);
	}
}
?>