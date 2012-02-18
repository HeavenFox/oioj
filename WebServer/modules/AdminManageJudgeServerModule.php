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
		case 'stats':
			$this->stats();
			break;
		case 'sync':
			$this->sync();
			break;
		case 'ping':
			$this->ping();
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
	
	public function stats()
	{
		$obj = JudgeServer::first(array('id','name','maxWorkload','ip','port'),'WHERE `id`='.IO::GET('id',0,'intval'));
		if ($obj)
		{
			$result = $obj->dispatch("STATS\n");
			if ($result)
			{
				echo "<p>Server {$obj->name} (#{$obj->id})</p>\n";
				echo "<p>Workload: {$result['Workload']}</p>";
			}
			else
			{
				echo '<p>Unable to contact server. It seems offline. You may try to ping the server.</p>';
			}
			
		}
	}
	
	public function sync()
	{
		$obj = JudgeServer::first(array('id','name','workload','ip','port'),'WHERE `id`='.IO::GET('id',0,'intval'));
		if ($obj)
		{
			$result = $obj->dispatch("STATS\n");
			if ($result)
			{
				echo "<p>Server {$obj->name} (#{$obj->id})</p>\n";
				echo "<p>Present Workload: {$obj->workload}</p>";
				$obj->workload = intval($result['Workload']);
				$obj->update();
				echo "<p>Updated to: {$result['Workload']}</p>";
			}
			else
			{
				echo '<p>Unable to contact server. It seems offline. You may try to ping the server.</p>';
			}
			
		}
	}
	
	public function ping()
	{
		$obj = JudgeServer::first(array('ip'),'WHERE `id`='.IO::GET('id',0,'intval'));
		if ($obj)
		{
			echo nl2br(IO::Ping($obj->ip));
		}
	}
}
?>