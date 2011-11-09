<?php
import('ActiveRecord');
import('Problem');
import('User');
class JudgeRecord extends ActiveRecord
{
	const STATUS_WAITING = 0;
	const STATUS_DISPATCHED = 1;
	const STATUS_ACCEPTED = 2;
	const STATUS_CE = 3;
	const STATUS_REJECTED = 4;
	
	
	static $tableName = 'oj_records';
	
	static $schema = array(
		'id' => array('class' => 'int'),
		'problem' => array('class' => 'Problem', 'comp' => 'one', 'column' => 'pid'),
		'status' => array('class' => 'int'),
		'server' => array('class' => 'JudgeServer', 'comp' => 'one', 'column' => 'server'),
		'lang' => array('class' => 'string'),
		'user' => array('class' => 'User', 'comp' => 'one', 'column' => 'uid'),
		'cases' => array('class' => 'string', 'setter' => 'serialize', 'getter' => 'unserialize'),
		'code' => array('class' => 'text'),
		'timestamp' => array('class' => 'int')
	);
		
	static $keyProperty = 'id';
	
	public $token;
	
	
	public function add()
	{
		$this->timestamp = time();
		parent::add();
	}
	
	public function parseCallback($general, $cases)
	{
		$gen = parseProtocol($general);
		
		if ($gen['Token'] != $this->token)
		{
			throw new Exception('Unauthorized access.');
		}
		
		$this->constructFromID($gen['RecordID']);
		$this->status = $gen['Status'];
		
		array_map("parseProtocol", $cases);
		
		$this->casesString = serialize($cases);
		
		import('JudgeServer');
		
		$server = new JudgeServer();
		$server->id = $this->serverID;
		$server->addWorkload(-1);
	}
	
	public function __toString()
	{
		$codeBase64 = base64_encode($this->code);
		return "JUDGE\nProblemID {$this->problemID}\nRecordID {$this->id}\nLang {$this->lang}\nSubmission {$codeBase64}";
	}
	
	public function dispatch($server = null)
	{
		if ($server instanceof JudgeServer)
		{
			if ($server->dispatch($record))
			{
				$server->addWorkload();
				$this->status = JudgeRecord::STATUS_DISPATCHED;
				$this->server = $server;
				$this->submit();
				return true;
			}
			return false;
		}
		else
		{
			while ($server = array_shift($servers))
			{
				if ($this->dispatch($server))
				{
					break;
				}
			}
		}
	}
}