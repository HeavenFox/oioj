<?php
class JudgeServer
{
	public $id;
	public $serverName;
	public $workload;
	public $maxWorkload;
	public $ip;
	public $port;
	
	public $cases;
	
	public function __construct($info = NULL)
	{
		if (is_int($info))
		{
			$this->constructFromID($info);
		}
		else if (is_array($info))
		{
			$this->constructFromRow($info);
		}
	}
	
	public function constructFromRow($row)
	{
		$this->id = $row['id'];
		$this->serverName = $row['name'];
		$this->ip = $row['ip'];
		$this->port = $row['port'];
	}
	
	public function constructFromID($id)
	{
		$this->id = $id;
		$DB = Database::Get();
		$stmt = $DB->prepare('SELECT name, ip, port, workload, max_workload FROM `oj_judgeservers` WHERE `id` = ?');
		$stmt->execute(array($id));
		$this->constructFromRow($stmt->fetch());
	}
	
	public static function GetAvailableServers()
	{
		$DB = Database::Get();
		$stmt = $DB->query('SELECT id, name, ip, port, workload, maxWorkload FROM oj_judgeservers WHERE workload < maxWorkload ORDER BY workload ASC');
		$servers = array();
		foreach ($stmt as $v)
		{
			$servers[] = new JudgeServer($v);
		}
		return $servers;
	}
	
	public function dispatch($task)
	{
		$address = "tcp://{$this->ip}:{$this->port}";
		$fp = stream_socket_client($address,$errno,$errstr);
		if (!$fp)
		{
			return false;
		}
		fwrite($fp,strval($task));
		fclose($fp);
		return true;
	}
	
	public function addWorkload($val = 1)
	{
		$DB = Database::Get();
		
		$stmt = $DB->prepare("UPDATE `oj_judgeservers` SET `workload` = `workload` + ({$val}) WHERE `id` = ?");
		$stmt->execute(array($this->id));
	}
}
?>