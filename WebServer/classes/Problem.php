<?php
import('ActiveRecord');
import('TestCase');
import('JudgeServer');
import('Settings');

class Problem extends ActiveRecord
{
	const TYPE_CLASSIC = 1;
	const TYPE_OUTPUT = 2;
	const TYPE_INTERACTIVE = 3;
	
	const IO_SCREEN = '/SCREEN/';
	
	const COMPARE_FULLTEXT = '/FULLTEXT/';
	const COMPARE_OMITSPACE = '/OMITSPACE/';
	
	public static $schema = array(
			'id' => array('class' => 'int'),
			'title' => array('class' => 'string'),
			'body' => array('class' => 'text'),
			'source' => array('class' => 'text'),
			'solution' => array('class' => 'text'),
			'type' => array('class' => 'int'),
			'input' => array('class' => 'string'),
			'output' => array('class' => 'string'),
			'compare' => array('class' => 'string'),
			'submission' => array('class' => 'int'),
			'accepted' => array('class' => 'int'),
			'listing' => array('class' => 'bool'),
			'user' => array('class' => 'User', 'comp' => 'one', 'column' => 'uid')
		);
	public static $tableName = 'oj_problems';
	public static $keyProperty = 'id';
	
	public static $LanguageMap = array(
			'c' => 'c',
			'cpp' => 'cpp',
			'cc' => 'cpp',
			'cxx' => 'cpp',
			'pas' => 'pas'
		);
	
	public $testCases = array();
	public $archiveLocation;
	public $dependencies;
	
	const DATA_PREFIX = 'data';
	const DATA_INPUT_SUFFIX = '.in';
	const DATA_ANSWER_SUFFIX = '.out';
	const DATA_SCORE = 10;
	
	public function addCaseByContent($input, $output, $tl, $ml, $score = self::DATA_SCORE)
	{
		$i = count($this->testCases)+1;
		$inputName = self::DATA_PREFIX . strval($i) . self::DATA_INPUT_SUFFIX;
		$answerName = self::DATA_PREFIX . strval($i) . self::DATA_ANSWER_SUFFIX;
		
		$curCase = new TestCase();
		$curCase->cid = $i;
		$curCase->problem = $this;
		$curCase->input = $inputName;
		$curCase->answer = $answerName;
		$curCase->timelimit = $tl;
		$curCase->memorylimit = $ml;
		$curCase->score = $score;
		
		$curCase->inputContent = $input;
		$curCase->answerContent = $output;
		
		$this->testCases[] = $curCase;
	}
	
	
	public function createArchive($location = null)
	{
		if (!$location)
		{
			$location = tempnam(sys_get_temp_dir(),'PBA');
		}
		$this->archiveLocation = $location;
		$zip = new ZipArchive();
		if ($zip->open($location,ZipArchive::CREATE))
		{
			foreach ($this->testCases as $k => $v)
			{
				$zip->addFromString($v->input,str_replace("\r",'',$v->inputContent));
				$zip->addFromString($v->answer,str_replace("\r",'',$v->answerContent));
			}
			$zip->close();
			return true;
		}
		return false;
	}
	
	public function dispatch($server)
	{
		if (!$this->archiveLocation)
		{
			throw new Exception('Archive not created');
		}
		if ($server->isLocal())
		{
			copy($this->archiveLocation,Settings::Get('local_judgeserver_data_dir').$this->id.'.zip');
			//$this->archiveLocation = null;
		}
		else
		{
			// FTP
			$ftp = ftp_connect($server->ip);
			
			
			if (!$ftp || !ftp_login($ftp,$server->ftpUsername,$server->ftpPassword))
			{
				throw new Exception('Unable to connect');
			}
			
			$file = fopen($this->archiveLocation,'rb');
			
			ftp_fput($ftp,$this->id.'.zip',$file,FTP_BINARY);
			
			fclose($file);
			
			ftp_close($ftp);
			
		}
		
		$server->dispatch($this->generateDispatchString());
		
	}
	
	public function purge()
	{
		unlink($this->archiveLocation);
	}
	
	private function generateDispatchString()
	{
		$str = "ADDPB\n";
		$str .= "1\n{$this->id} {$this->type} {$this->compare} {$this->input} {$this->output}\n";
		$str .= strval(count($this->testCases)) . "\n";
		foreach ($this->testCases as $c)
		{
			$str .= "{$c->cid} {$c->input} {$c->answer} {$c->timelimit} {$c->memorylimit} {$c->score}\n";
		}
		// TODO add dependency support
		$str .= "0\n";
		$str .= "zip\n";
		
		return $str;
	}
	
	public function submissionPlusOne()
	{
		Database::Get()->query('UPDATE `oj_problems` SET `submission` = `submission` + 1 WHERE `id` = '.intval($this->id));
	}
	
	public function acceptedPlusOne()
	{
		Database::Get()->query('UPDATE `oj_problems` SET `accepted` = `accepted` + 1 WHERE `id` = '.intval($this->id));
	}
	
	public function add()
	{
		parent::add();
		foreach ($this->testCases as $c)
		{
			$c->problem = $this;
			$c->submit();
		}
	}
}
?>