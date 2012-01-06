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
	
	public $tokens;
	
	private $usedToken;
	
	public $localUrl = null;
	
	
	public function add()
	{
		$this->timestamp = time();
		parent::add();
	}
	
	public function setTokens()
	{
		$this->tokens = array(Settings::Get('token'));
		if (strlen($s = Settings::Get('backup_token')) > 0)
		{
			$this->tokens[] = $s;
		}
	}
	
	public function parseCallback($general, $cases)
	{
		$gen = parseProtocol($general);
		$this->setTokens();
		if (!in_array(trim($gen['Token']), $this->tokens))
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
		
		$this->fetch(array(),array('server' => array('id')));
		$this->server->addWorkload(-1);
		
		
	}
	
	public function __toString()
	{
		$codeBase64 = base64_encode($this->code);
		return "JUDGE\nProblemID {$this->problem->id}\nRecordID {$this->id}\nLang {$this->lang}\nSubmission {$codeBase64}\nToken {$this->usedToken}";
	}
	
	public function dispatch($server = null)
	{
		if ($server instanceof JudgeServer)
		{
			
			foreach ($this->tokens as $token)
			{
				$this->usedToken = $token;
				if ($server->dispatch($this))
				{
					$server->addWorkload();
					$this->status = JudgeRecord::STATUS_DISPATCHED;
					$this->server = $server;
					
					$this->submit();
					return true;
				}
			}
			return false;
		}
		else
		{
			$servers = (is_array($server) ? $server : JudgeServer::GetAvailableServers());
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
	
	public function getReadableStatus()
	{
		$statusStr = array('Waiting','Dispatched','Accepted','Compile Error','Rejected');
		return $statusStr[$this->status];
	}
}