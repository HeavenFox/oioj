<?php
import('ActiveRecord');
import('JudgeRecord');
class Contest extends ActiveRecord
{
	const STATUS_WAITING = 1;
	const STATUS_INPROGRESS = 2;
	const STATUS_FINISHED = 3;
	const STATUS_JUDGING = 4;
	const STATUS_JUDGED = 5;
	
	static $StatusReadable = array(
		null,'Waiting','In Progress','Finished','Judging','Judged'
	);
	
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
		'participants' => array('class' => 'User', 'comp' => 'many', 'junction' => 'oj_contest_register', 'column' => array('cid','uid'))
	);
	
	private $option;
	
	public function startContest()
	{
		$this->getComposite(array('problems' => array('id')));
		foreach ($this->problems as $p)
		{
			$p->listing = 1;
			$p->submit();
		}
		$this->status = self::STATUS_INPROGRESS;
		$this->submit();
	}
	
	public function endContest()
	{
		$this->status = self::STATUS_FINISHED;
		$this->submit();
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
		return isset($this->option[$option]) && $this->option[$option];
	}
	
	public function handleSubmit($problem, $name)
	{
		if ($this->getOption('after_submit') == 'save')
		{
			
		}
	}
	
	public function generateRanking()
	{
		if (($this->getOption('after_submit') == 'judge' && $this->getOption('show_realtime_rank')) || $this->status == self::STATUS_JUDGED)
		{
			// Score, Num Right, Total Time, Num Wrong
			'SELECT FROM `oj_contest_register` ';
		}
	}
	
	public function judge($callback)
	{
		if ($this->getOption('after_submit') == 'save')
		{
			$db = Database::Get();
			
			foreach ($db->query('SELECT `pid`,`uid`,`code`,`lang` FROM `oj_contest_submissions` WHERE `cid` = '.$this->id.' GROUP BY `uid`,`pid` HAVING `timestamp` = MAX(`timestamp`)') as $row)
			{
				$rec = new JudgeRecord();
				$rec->problem = new Problem($row['pid']);
				$rec->user = new User($row['uid']);
				$rec->code = $row['code'];
				$rec->lang = $row['lang'];
				$rec->dispatch();
			}
		}
	}
	
	/**
	 * Check enrollment status of a user
	 */
	public function checkEnrollment($user)
	{
		$registered = false;
		if ($user->id != 0)
		{
			if (($sess = IO::Session('contest-registered-'.$this->id,-1)) == -1)
			{
				$db = Database::Get();
				$stmt = $db->query('SELECT count(*) FROM `oj_contest_register` WHERE `cid` = '.$this->id.' AND `uid` = '.$user->id)->fetch();
				if ($stmt[0] > 0)
				{
					$registered = true;
				}else
				{
					$registered = false;
				}
				IO::SetSession('contest-registered-'.$this->id,$registered);
			}
			else
			{
				$registered = $sess;
			}
			
		}
		return $registered;
	}
	
	/**
	 * Check if user has started working on a contest
	 * @return mixed false if has not begin, time started otherwise
	 */
	public function checkStarted($user)
	{
		$beginTime = false;
		if ($user->id != 0)
		{
			if (($sess = IO::Session('contest-began-'.$this->id,false,'intval')) === false)
			{
				$db = Database::Get();
				$stmt = $db->query('SELECT `started` FROM `oj_contest_register` WHERE `cid` = '.$this->id.' AND `uid` = '.$user->id)->fetch();
				if (!is_null($stmt[0]))
				{
					$beginTime = intval($stmt[0]);
					IO::SetSession('contest-began-'.$this->id,$beginTime);
				}
			}
			else
			{
				$beginTime = intval($sess);
			}
		}
		return $beginTime;
	}
	
	public function userStart($user,$time)
	{
		if ($user->id > 0)
		{
			$db = Database::Get();
			$db->query('UPDATE `oj_contest_register` SET `started` = '.$time.' WHERE `cid` = '.$this->id.' AND `uid` = '.$user->id);
		}
	}
	
	public function submitSolution($problemID, $userID, $code, $lang)
	{
		$db = Database::Get();
		$stmt = $db->prepare("INSERT INTO `oj_contest_submissions` (cid,uid,pid,code,lang,timestamp) VALUES (?,?,?,?,?,?)");
		$stmt->execute(array($this->id,$problemID,$userID,$code,$lang,time()));
	}
}
?>