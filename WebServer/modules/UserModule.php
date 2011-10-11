<?php
IN_OIOJ || die('Forbidden');

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
		case 'getcaptcha':
			$this->getCAPTCHA();
			break;
		}
	}
	
	public function doLogin()
	{
		
	}
	
	public function getCAPTCHA()
	{
		require_once LIB_DIR . 'recaptchalib.php';
		echo recaptcha_get_html(Config::$CAPTCHA_Public);
	}
}
?>