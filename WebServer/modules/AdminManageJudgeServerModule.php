<?php
defined('IN_OIOJ') || die('Forbidden');

import('JudgeServer');
class AdminManageJudgeServerModule
{
	public function run()
	{
		User::GetCurrent()->assertAble('manage_judgeserver');
		
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
		default:
			$servers = JudgeServer::find(array('id','name','ip','port','workload','maxWorkload','ftpUsername','online'));
			OIOJ::$template->assign('servers',$servers);
			OIOJ::$template->display('admin_managejudgeserver.tpl');
		}
		
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