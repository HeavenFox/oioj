<?php

define('RECORDSTATUS_WAITING', 0);
define('RECORDSTATUS_DISPATCHED', 1);
define('RECORD', 2);
define('RECORD', 3);
define('RECORD', 4);

class JudgeRecord
{
	const STATUS_WAITING = 0;
	const STATUS_DISPATCHED = 1;
	const STATUS_ACCEPTED = 2;
	const STATUS_CE = 3;
	const STATUS_REJECTED = 4;
	
	
	public $problemID;
	public $recordID;
	public $lang;
	
	private $casesString;
	
	private $codeBase64;
	
	public function setSubmission($code)
	{
		$this->codeBase64 = base64_encode($code);
	}
	
	public function __toString()
	{
		return "ProblemID {$this->problemID}\nRecordID {$this->recordID}\nLang {$this->lang}\nSubmission {$this->codeBase64}";
	}
	
	public function submit()
	{
		if ($this->id)
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
		$this->id = $DB->lastInsertId();
	}
	
	private function updateRecord()
	{
	
	}
	
	public function parseCallback($general, $cases)
	{
		$gen = parseProtocol($general);
		
		array_map("parseProtocol", $cases);
		
		$this->casesString = serialize($cases);
	}
}