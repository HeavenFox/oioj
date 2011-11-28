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
		'score' => array('class' => 'int'),
		'timestamp' => array('class' => 'int')
	);
		
	static $keyProperty = 'id';
	
	public $token;
	
	public $localUrl = null;
	
	
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
		
		$this->id = $gen['RecordID'];
		$this->status = $gen['Status'];
		
		array_map("parseProtocol", $cases);
		
		$this->score = 0;
		foreach ($cases as $k => $c)
		{
			$cases[$k] = parseProtocol($c);
			$this->score += intval($cases[$k]['CaseScore']);
		}
		
		$this->cases = $cases;
		
		import('JudgeServer');
		
		$server = new JudgeServer($this->server);
		$server->addWorkload(-1);
	}
	
	public function __toString()
	{
		$codeBase64 = base64_encode($this->code);
		return "JUDGE\nProblemID {$this->problem->id}\nRecordID {$this->id}\nLang {$this->lang}\nSubmission {$codeBase64}";
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
			$servers = JudgeServer::GetAvailableServers();
			while ($server = array_shift($servers))
			{
				if ($this->dispatch($server))
				{
					return true;
				}
			}
		}
		return false;
	}
}