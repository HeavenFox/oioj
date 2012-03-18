<?php
import('ActiveRecord');
class JudgeServer extends ActiveRecord
{
	static $tableName = 'oj_judgeservers';
	
	const RESPONSE_MAX_LENGTH = 1024;
	
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
	
	public static function GetTokens()
	{
		$tokens = array(Settings::Get('token'));
		if (strlen($s = Settings::Get('backup_token')) > 0)
		{
			$tokens[] = $s;
		}
		return $tokens;
	}
	
	public static function GetAvailableServers()
	{
		return self::find(array('id','name','ip','port','workload','maxWorkload'),'WHERE workload < maxWorkload AND `online` = 1 ORDER BY workload ASC');
	}
	
	public function dispatch($task, $timeout = 10)
	{
		$fp = @fsockopen($this->ip,$this->port,$errno,$errstr,$timeout);
		if (!$fp)
		{
			return false;
		}
		$data = strval($task);
		fwrite($fp,pack('I',strlen($data)).$data);
		$res = fread($fp,self::RESPONSE_MAX_LENGTH);
		fclose($fp);
		if (strlen(trim($res)) > 0)
		{
			return new SimpleXMLElement($res);
		}
		
		return true;
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