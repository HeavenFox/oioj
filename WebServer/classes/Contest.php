<?php
import('ActiveRecord');
class Contest extends ActiveRecord
{
	const STATUS_WAITING = 1;
	const STATUS_INPROGRESS = 2;
	const STATUS_FINISHED = 3;
	const STATUS_JUDGING = 4;
	const STATUS_JUDGED = 5;
	
	static $tableName = 'oj_contests';
	static $schema = array(
		'id' => array('class' => 'int'),
		'title' => array('class' => 'string'),
		'description' => array('class' => 'text'),
		'status' => array('class' => 'int'),
		'beginTime' => array('class' => 'int','column' => 'begin_time'),
		'endTime' => array('class' => 'int','column' => 'end_time'),
		'regBegin' => array('class' => 'int','column' => 'reg_begin'),
		'regDeadline' => array('class' => 'int','column' => 'reg_deadline'),
		'duration' => array('class' => 'int'),
		'user' => array('class' => 'User','comp' => 'one','column' => 'uid'),
		'problems' => array('class' => 'Problem', 'comp' => 'many', 'junction' => 'oj_contest_problems', 'column' => array('cid','pid')),
		'registrants' => array('class' => 'User', 'comp' => 'many', 'junction' => 'oj_contest_register', 'column' => array('cid','uid'))
	);
	
	private $options;
	
	public function startContest()
	{
		
	}
	
	public function endContest()
	{
		
	}
	
	public function getOption($option)
	{
		if (!$this->option)
		{
			$this->option = array();
			$db = Database::Get();
			$stmt = $db->query('SELECT `key`,`value` FROM `oj_contest_options` WHERE `cid`='.intval($this->id));
			foreach ($stmt as $r)
			{
				$this->option[$r['key']] = $r['value'];
			}
		}
		return $this->option[$option];
	}
	
	public function handleSubmit($problem, $name)
	{
		if ($this->getOption('after_submit') == 'save')
		{
			
		}
	}
	
	public function generateRanking()
	{
		if ($this->getOption('after_submit') == 'judge' || $this->status == self::STATUS_JUDGED)
		{
			
		}
	}
	
	public function judge()
	{
		
	}
}
?>