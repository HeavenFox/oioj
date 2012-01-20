<?php
import('ActiveRecord');
class JudgeServer extends ActiveRecord
{
	static $tableName = 'oj_judgeservers';
	
	const RESPONSE_MAX_LENGTH = 512;
	
	static $schema = array(
		'id' => array('class' => 'int'),
		'name' => array('class' => 'string'),
		'workload' => array('class' => 'int'),
		'maxWorkload' => array('class' => 'int'),
		'ip' => array('class' => 'string'),
		'port' => array('class' => 'int'),
		'ftpUsername' => array('class' => 'string', 'column' => 'ftp_username'),
		'ftpPassword' => array('class' => 'string', 'column' => 'ftp_password'),
		'online' => array('class' => 'bool')
	);
	static $keyProperty = 'id';
	
	public $cases;
	
	public static function GetAvailableServers()
	{
		return self::find(array('id','name','ip','port','workload','maxWorkload'),'WHERE workload < maxWorkload AND `online` = 1 ORDER BY workload ASC');
	}
	
	public function dispatch($task)
	{
		$fp = fsockopen($this->ip,$this->port,$errno,$errstr,10);
		if (!$fp)
		{
			return false;
		}
		$data = strval($task);
		fwrite($fp,pack('I',strlen($data)).$data);
		$res = parseProtocol(fread($fp,self::RESPONSE_MAX_LENGTH));
		fclose($fp);
		return $res;
	}
	
	public function addWorkload($val = 1)
	{
		$DB = Database::Get();
		
		$stmt = $DB->exec("UPDATE `oj_judgeservers` SET `workload` = `workload` + ({$val}) WHERE `id` = {$this->id}");
	}
	
	public function setWorkload($val)
	{
		$DB = Database::Get();
		
		$stmt = $DB->exec("UPDATE `oj_judgeservers` SET `workload` = {$val} WHERE `id` = {$this->id}");
	}
	
	public function isLocal()
	{
		return $this->ip == '127.0.0.1' || $this->ip == 'localhost';
	}
}
?>