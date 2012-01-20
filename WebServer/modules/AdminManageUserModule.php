<?php
defined('IN_OIOJ') || die('Forbidden');

import('User');
class AdminManageUserModule
{
	public function run()
	{
		$user = User::GetCurrent();
		if (!($user->ableTo('manage_user') || ($user->ableTo('admin_cp') && !$user->unableTo('manage_user'))))
		{
			throw new PermissionException();
		}
		switch (IO::GET('act'))
		{
		case 'add':
			$this->add();
			break;
		case 'edit':
			$this->edit();
			break;
		case 'save':
			$this->save();
			break;
		case 'invitation':
			$this->invitation();
			break;
		default:
			
		}
		
	}
	
	public function invitation()
	{
		import('Invitation');
		if (IO::GET('do') == 'generate')
		{
			$num = IO::POST('count',5,'intval');
			
			for ($i=0;$i<$num;$i++)
			{
				$n = new Invitation();
				$n->code = md5(rand()*time());
				$n->submit();
			}
		}
		$invitations = Invitation::find(array('code','user'=>array('username')),'WHERE `sender` IS NULL');
		OIOJ::$template->assign('invitations',$invitations);
		OIOJ::$template->display('admin_user_invitation.tpl');
	}
	
	public function add()
	{
		OIOJ::$template->display('admin_editjudgeserver.tpl');
	}
	
	public function edit()
	{
		$obj = JudgeServer::first(array('id','name','ip','port','maxWorkload','ftpUsername','online'),IO::GET('id',0,'intval'));
		OIOJ::$template->assign('server',$obj);
		OIOJ::$template->display('admin_editjudgeserver.tpl');
	}
	
	public function save()
	{
		$obj = new JudgeServer;
		if ($id = IO::POST('id'))
		{
			$obj->id = $id;
		}
		
		$obj->online = IO::POST('online',0,function($data){return 1;});
		
		$obj->name = IO::POST('name');
		$obj->ip = IO::POST('ip');
		$obj->port = IO::POST('port',0,'intval');
		$obj->ftpUsername = IO::POST('ftp_username');
		$obj->maxWorkload = IO::POST('max_workload');
		if (IO::POST('ftp_password'))
		{
			$obj->ftpPassword = IO::POST('ftp_password');
		
		}
		
		$obj->submit();
		
		OIOJ::Redirect('Judge Server Saved Successfully','index.php?mod=admin_judgeserver');
	}
}
?>