<?php

class JudgeRecord
{
	const STATUS_WAITING = 0;
	const STATUS_DISPATCHED = 1;
	const STATUS_ACCEPTED = 2;
	const STATUS_CE = 3;
	const STATUS_REJECTED = 4;
	
	
	public $problemID = 0;
	public $recordID = 0;
	public $lang;
	
	public $userID = 1;
	
	public $serverID = 0;
	
	public $status = 0;
	
	public $timestamp;
	
	private $casesString = '';
	
	public $code = '';
	
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
		if (isset($row['id'])) $this->recordID = $row['id'];
		if (isset($row['pid'])) $this->problemID = $row['pid'];
		if (isset($row['status'])) $this->status = $row['status'];
		if (isset($row['server'])) $this->serverID = $row['server'];
		if (isset($row['lang'])) $this->lang = $row['lang'];
		if (isset($row['uid'])) $this->userID = $row['uid'];
		if (isset($row['cases']))$this->casesString = $row['cases'];
		if (isset($row['timestamp']))$this->timestamp = $row['timestamp'];
	}
	
	public function constructFromID($id)
	{
		$this->recordID = $id;
		$DB = Database::Get();
		$stmt = $DB->prepare('SELECT pid,status,server,lang,uid,code,cases,timestamp FROM `oj_records` WHERE `id` = ?');
		$stmt->execute(array($id));
		$this->constructFromRow($stmt->fetch());
	}
	
	public function __toString()
	{
		$codeBase64 = base64_encode($this->code);
		echo "ProblemID {$this->problemID}\nRecordID {$this->recordID}\nLang {$this->lang}\nSubmission {$codeBase64}";
		return "ProblemID {$this->problemID}\nRecordID {$this->recordID}\nLang {$this->lang}\nSubmission {$codeBase64}";
	}
	
	public function submit()
	{
		if ($this->recordID)
		{
			$this->updateRecord();
		}
		else
		{
			$this->addRecord();
		}
	}
	
	private function addRecord()
	{
		$DB = Database::Get();
		$stmt = $DB->prepare('INSERT INTO `oj_records` (pid,status,server,lang,uid,code,cases,timestamp) VALUES (?,?,?,?,?,?,?,?)');
		$stmt->execute(array($this->problemID, $this->status, $this->serverID, $this->lang, $this->userID, $this->code, $this->casesString, time()));
		$this->recordID = $DB->lastInsertId();
		echo $this->recordID;
	}
	
	private function updateRecord()
	{
		$DB = Database::Get();
		$stmt = $DB->prepare('UPDATE `oj_records` SET status = ?, cases = ?, server = ? WHERE `id` = ?');
		$stmt->execute(array($this->status, $this->casesString, $this->serverID, $this->recordID));
	}
	
	public function parseCallback($general, $cases)
	{
		$gen = parseProtocol($general);
		$this->constructFromID($gen['RecordID']);
		$this->status = $gen['Status'];
		
		array_map("parseProtocol", $cases);
		
		$this->casesString = serialize($cases);
	}
}