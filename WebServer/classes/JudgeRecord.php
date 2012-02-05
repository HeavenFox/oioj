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
		'timestamp' => array('class' => 'int'),
		
		'count' => array('class' => 'int', 'query' => 'count(`id`)')
	);
		
	static $keyProperty = 'id';
	
	public $tokens;
	
	private $usedToken;
	
	public $localUrl = null;
	
	public $statusClass;
	public $statusString;
	
	public static function popWaitlist()
	{
		$db = Database::Get();
		$db->beginTransaction();
		$rec = JudgeRecord::first(array('id','lang','code','problem' => array('id')),'WHERE `status` = '.self::STATUS_WAITING.' ORDER BY `timestamp` ASC');
		$rec->setTokens();
		$rec->dispatch();
		$db->commit();
	}
	
	public static function PopAllWaitlist()
	{
		$db = Database::Get();
		$db->beginTransaction();
		$recs = JudgeRecord::find(array('id','lang','code','problem' => array('id')),'WHERE `status` = '.self::STATUS_WAITING.' ORDER BY `timestamp` ASC');
		foreach ($recs as $rec)
		{
			$rec->setTokens();
			if (!$rec->dispatch())
			{
				// Likely maximum capacity
				break;
			}
		}
		$db->commit();
	}
	
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
		
		$this->id = intval($gen['RecordID']);
		$this->status = intval($gen['Status']);
		
		array_map("parseProtocol", $cases);
		
		$this->score = 0;
		foreach ($cases as $k => $c)
		{
			$cases[$k] = parseProtocol($c);
			$this->score += intval($cases[$k]['CaseScore']);
		}
		
		$this->cases = $cases;
		
		import('JudgeServer');
		
		$this->fetch(array('server' => array('id'),'problem'=>array('id')));
		$this->server->addWorkload(-1);
		$this->problem->updateSubmissionStats(1, $this->status == self::STATUS_ACCEPTED ? 1 : 0);
	}
	
	public function __toString()
	{
		$codeBase64 = base64_encode($this->code);
		return "JUDGE\nProblemID {$this->problem->id}\nRecordID {$this->id}\nLang {$this->lang}\nSubmission {$codeBase64}\nToken {$this->usedToken}\n";
	}
	
	public function dispatch($server = null)
	{
		if ($server instanceof JudgeServer)
		{
			foreach ($this->tokens as $token)
			{
				$this->usedToken = $token;
				$result = $server->dispatch($this);
				if ($result && $result['ServerCode'] <= 1)
				{
					//$server->setWorkload(intval($result['Workload']));
					$server->addWorkload(1);
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