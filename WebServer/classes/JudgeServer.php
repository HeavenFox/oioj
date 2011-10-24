<?php
import('ActiveRecord');
class JudgeServer extends ActiveRecord
{
	static $tableName = 'oj_judgeservers';
	
	static $schema = array(
		'id' => array('class' => 'int'),
		'name' => array('class' => 'string'),
		'workload' => array('class' => 'int'),
		'maxWorkload' => array('class' => 'int'),
		'ip' => array('class' => 'string'),
		'port' => array('class' => 'int'),
		'ftpUsername' => array('class' => 'string', 'column' => 'ftp_username'),
		'ftpPassword' => array('class' => 'string', 'column' => 'ftp_password')
	);
	static $keyProperty = 'id';
	
	public $cases;
	
	public static function GetAvailableServers()
	{
		return self::find(array('id','name','ip','port','workload','maxWorkload'),null,'WHERE workload < maxWorkload ORDER BY workload ASC');
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
	
	public function isLocal()
	{
		return $this->ip == '127.0.0.1';
	}
}
?>