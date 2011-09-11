<?php
class JudgeServer
{
	private static $servers;
	
	public $id;
	public $serverName;
	public $workload;
	public $ip;
	public $port;
	
	public static function LoadAvailableServers()
	{
		
	}
	
	public static function GetAvailableServer()
	{
		
	}
	
	public function assignTask($task)
	{
		$address = "tcp://{$ip}:{$port}";
		$fp = stream_socket_client($address,$errno,$errstr);
		fwrite($fp,strval($task));
		fclose($fp);
	}
}
?>