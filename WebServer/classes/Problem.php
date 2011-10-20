<?php
import('ActiveRecord');
import('TestCase');
class Problem extends ActiveRecord
{
	public static $schema = array(
			'id' => array('class' => 'int'),
			'title' => array('class' => 'string'),
			'body' => array('class' => 'text'),
			'type' => array('class' => 'int'),
			'input' => array('class' => 'string'),
			'output' => array('class' => 'string'),
			'compare' => array('class' => 'string'),
			'submission' => array('class' => 'int'),
			'listing' => array('class' => 'bool'),
			'user' => array('class' => 'User', 'comp' => 'one', 'column' => 'uid')
		);
	public static $tableName = 'oj_problems';
	public static $keyProperty = 'id';
	
	public $testCases = array();
	public $archiveLocation;
	public $dependencies;
	
	const DATA_PREFIX = 'data';
	const DATA_INPUT_SUFFIX = '.in';
	const DATA_ANSWER_SUFFIX = '.out';
	
	public function addCaseByContent($input, $output, $tl, $ml)
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
		
		$this->testCases[] = $curCase;
	}
	
	
	public function createArchive($location)
	{
		$this->archiveLocation = $location;
		$zip = new ZipArchive();
		if ($zip->open($location,ZipArchive::CREATE))
		{
			foreach ($this->testCases as $k => $v)
			{
				$zip->addFromString($v->input,$v->inputContent);
				$zip->addFromString($v->answer,$v->answerContent);
			}
			$zip->close();
			return true;
		}
		return false;
	}
	
	public function add()
	{
		
		parent::add();
		
	}
}
?>