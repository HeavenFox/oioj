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
		'cases' => array('class' => 'string', 'serializer' => 'serialize', 'unserializer' => 'unserialize'),
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
		if ($rec)
		{
			$rec->dispatch();
		}
		$db->commit();
	}
	
	public static function PopAllWaitlist()
	{
		$db = Database::Get();
		$db->beginTransaction();
		$recs = JudgeRecord::find(array('id','lang','code','problem' => array('id')),'WHERE `status` = '.self::STATUS_WAITING.' ORDER BY `timestamp` ASC');
		foreach ($recs as $rec)
		{
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
	
	public function getTokens()
	{
		$tokens = array(Settings::Get('token'));
		if (strlen($s = Settings::Get('backup_token')) > 0)
		{
			$tokens[] = $s;
		}
		return $tokens;
	}
	
	public function parseCallback($xmlData)
	{
		$xml = new SimpleXMLElement($xmlData);
		error_log(var_export((string)$xml['type'],true));
		error_log(var_export((string)$xml['token'],true));
		error_log(var_export($this->getTokens(),true));
		if (!in_array((string)$xml[0]['token'], $this->getTokens()))
		{
			throw new Exception('Unauthorized access.');
		}
		
		$this->id = intval($xml->record[0]['id']);
		$this->status = intval($xml->record[0]['status']);
		
		$this->score = 0;
		$cases = array();
		
		foreach ($xml->cases->{'case'} as $c)
		{
			$curcase = array();
			
			$curcase['id'] = intval($c['id']);
			$curcase['result'] = intval($c['result']);
			$curcase['score'] = intval($c['score']);
			$this->score += $curcase['score'];
			$curcase['time'] = (string)$c['time'];
			$curcase['memory'] = (string)$c['memory'];
			if (isset($c['detail']))
			{
				$curcase['detail'] = (string)$c['detail'];
			}
			
			$cases[] = $curcase;
		}
		
		$this->cases = $cases;
		
		import('JudgeServer');
		
		$this->fetch(array('server' => array('id'),'problem' => array('id')));
		$this->server->addWorkload(-1);
		$this->problem->updateSubmissionStats(1, $this->status == self::STATUS_ACCEPTED ? 1 : 0);
		
		$this->update();
	}
	
	public function __toString()
	{
		$codeBase64 = base64_encode($this->code);
		
		$requestNode = new SimpleXMLElement('<request />');
		$requestNode->addAttribute('type','judge');
		$requestNode->addAttribute("token",Settings::Get("token"));
		if (strlen(Settings::Get('backup_token')) > 0)
		{
			$requestNode->addAttribute("backup-token",Settings::Get("backup_token"));
		}
		
		$requestNode->addAttribute("version","2.0");
		
		$problemNode = $requestNode->addChild('problem');
		$problemNode->addAttribute("id",$this->problem->id);
		
		$recordNode = $requestNode->addChild('record');
		$recordNode->addAttribute("id",$this->id);
		
		$submissionNode = $requestNode->addChild('submission', $codeBase64);
		$submissionNode->addAttribute('lang', $this->lang);
		$submissionNode->addAttribute('encoding','base64');
		return $requestNode->asXML();
	}
	
	public function dispatch($server = null)
	{
		if ($server instanceof JudgeServer)
		{
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