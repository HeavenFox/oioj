<?php
IN_OIOJ || die('Forbidden');

import('Invitation');
import('User');
import('GuestUser');

class UserModule
{
	public function autoload()
	{
		// Begin session
		session_start();
		
		OIOJ::$template->assign('user',User::GetCurrent());
	}
	
	public function run()
	{
		switch (IO::GET('act'))
		{
		case 'login':
			$this->doLogin();
			break;
		case 'register_submit':
			$this->doRegisterSubmit();
			break;
		case 'getcaptcha':
			$this->getCAPTCHA();
			break;
		}
	}
	
	public function doLogin()
	{
		OIOJ::InitDatabase();
		$username = IO::POST('username','',null);
		$password = IO::POST('password','',null);
		
		
		$obj = User::first(array('id','username','password'),null,'WHERE `username` = ? AND `password` = SHA1(CONCAT(?,`salt`))',array($username,$password));
		
		if (!$obj)
		{
			throw new Exception('Username or password incorrect');
		}
		
		if (IO::POST('remember'))
		{
			IO::SetCookie('uid',$obj->id,14*24*3600);
			IO::SetCookie('password',$obj->password,14*24*3600);
		}
		
		$obj->createSession();
		
		OIOJ::Redirect('You have been logged in.');
		
	}
	
	public function doRegisterSubmit()
	{
		OIOJ::InitDatabase();
		$suppliedCode = IO::POST('invitation',null,null);
		
		$invit = Invitation::first(array('id','user'),null,'WHERE `code` = ?', array($suppliedCode));
		
		if (!$invit || intval($invit->user))
		{
			throw new Exception('Sorry, invitation code invalid');
		}
		
		$user = new User();
		
		if (IO::POST('password') != IO::POST('password_confirm'))
		{
			throw new Exception('Password don\'t match');
		}
		
		$user->username = IO::POST('username');
		$user->password = IO::POST('password');
		$user->email = IO::POST('email');
		
		$user->add();
		
		$invit->user = $user;
		$invit->update();
		
		$user->createSession();
		
		OIOJ::Redirect('Successfully registered!');
	}
	
	public function getCAPTCHA()
	{
		require_once LIB_DIR . 'recaptchalib.php';
		echo recaptcha_get_js(Config::$CAPTCHA_Public);
	}
}
?>