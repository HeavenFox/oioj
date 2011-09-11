<?php

define('RECORDSTATUS_WAITING', 0);
define('RECORDSTATUS_DISPATCHED', 1);
define('RECORDSTATUS_ACCEPTED', 2);
define('RECORDSTATUS_CE', 3);
define('RECORDSTATUS_REJECTED', 4);

class JudgeRecord
{
	public $problemID;
	public $recordID;
	public $lang;
	
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
}