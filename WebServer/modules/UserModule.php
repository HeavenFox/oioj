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
		
		OIOJ::$template->assign('current_user',User::GetCurrent());
	}
	
	public function run()
	{
		switch (IO::GET('act'))
		{
		case 'login':
			$this->doLogin();
			break;
		case 'loginform':
			$this->showLoginForm();
			break;
		case 'register':
			$this->register();
			break;
		case 'register_submit':
			$this->doRegisterSubmit();
			break;
		case 'getcaptcha':
			$this->getCAPTCHA();
			break;
		case 'logout':
			$this->logout();
			break;
		case 'tagcomplete':
			$this->completeTag();
			break;
		}
	}
	
	public function generateRegisterForm()
	{
		import('SmartyForm');
		
		$form = new SmartyForm('register','index.php?mod=user&act=register_submit');
		
		$form->add(new SF_TextField('id','username','label','Username','minLength',3,'maxLength',16));
		
		$form->get('username')->addValidator(function($v){
			if (preg_match('/[<>\/\\\\$\|]/',$v))
			{
				throw new InputException('Please do not use special character');
			}
		});
		
		$form->add(new SF_Password('id','password','label','Password','minLength',6));
		$form->add(new SF_Password('id','password_confirm','label','Again'));
		$form->add(new SF_EMail('id','email','label','Email'));
		$form->add(new SF_TextField('id','invitation','label','Invitation'));
		
		return $form;
	}
	
	public function doLogin()
	{
		$username = IO::POST('username','',null);
		$password = IO::POST('password','',null);
		
		if (!$username || !$password)
		{
			throw new Exception('You have to provide password');
		}
		
		User::Login($username, $password, IO::POST('remember'));
		
		OIOJ::Redirect('You have been logged in.');
		
	}
	
	public function logout()
	{
		User::DestroySession();
		OIOJ::Redirect('You have been logged out.');
	}
	
	public function register()
	{
		OIOJ::$template->assign('sf_register', $this->generateRegisterForm());
		$this->registerForm();
	}
	
	public function registerForm()
	{
		OIOJ::$template->display('register.tpl');
	}
	
	public function showLoginForm()
	{
		OIOJ::$template->display('boxes/login.tpl');
	}
	
	public function doRegisterSubmit()
	{
		$form = $this->generateRegisterForm();
		
		$form->gatherFromPOST();
		
		$user = new User();
		
		$form->addRecord('user', $user);
		$form->bind('username','user');
		$form->bind('password','user');
		$form->bind('email','user');
		
		$form->get('invitation')->addValidator(function($suppliedCode){
			$invit = Invitation::first(array('id','user'),'WHERE `code` = ?', array($suppliedCode));
			
			if (!$invit || intval($invit->user))
			{
				throw new InputException('Invitation code invalid');
			}
		});
		
		$form->get('password_confirm')->addValidator(function($data) use ($form){
			if ($data != $form->get('password')->data)
			{
				throw new InputException('Two passwords do not match');
			}
		});
		
		$form->validate();
		
		if ($form->valid)
		{
			$form->saveToRecord();
			
			try
			{
				$user->add();
			}
			catch(PDOException $e)
			{
				$form->get('username')->triggerError('Sorry, but the username is taken');
			}
		}
		
		if ($form->valid)
		{
			// Automatically log user in
			$user->createSession();
			
			// Invalidate invitation
			$stmt = Database::Get()->prepare('UPDATE `oj_invitations` SET `user` = ? WHERE `code` = ?');
			
			// Add Tags
			Database::Get()->exec("INSERT INTO `oj_user_tags` (`uid`,`tid`) SELECT {$user->id},`tid` FROM `oj_usertag_properties` WHERE `key`='auto_apply_new_user'");
			
			$stmt->execute(array($user->id, $form->get('invitation')->data));
			
			OIOJ::Redirect('You have successfully registered!');
		}
		else
		{
			OIOJ::$template->assign('sf_register', $form);
			$this->registerForm();
		}
		
	}
	
	public function getCAPTCHA()
	{
		require_once LIB_DIR . 'recaptchalib.php';
		echo recaptcha_get_js(Config::$CAPTCHA_Public);
	}
	
	public function completeTag()
	{
		$tags = User::SearchTags('%'.IO::REQUEST('term').'%', 10);
		$result = array();
		foreach ($tags as $tag)
		{
			$n = array();
			$n['label'] = $tag->tag;
			$n['value'] = $tag->id;
			$result[] = $n;
		}
		echo json_encode($result);
	}
}
?>