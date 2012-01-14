<?php
import('ActiveRecord');
import('JudgeRecord');
import('ContestParticipant');
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
		'publicity' => array('class' => 'int'),
		'user' => array('class' => 'User','comp' => 'one','column' => 'uid'),
		'problems' => array('class' => 'Problem', 'comp' => 'many', 'junction' => 'oj_contest_problems', 'column' => array('cid','pid')),
		'participants' => array('class' => 'User', 'comp' => 'many', 'junction' => 'oj_contest_register', 'column' => array('cid','uid'))
	);
	
	private $option;
	
	public function startContest()
	{
		/*
		$this->getComposite(array('problems' => array('id')));
		foreach ($this->problems as $p)
		{
			$p->listing = 1;
			$p->submit();
		}
		*/
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
		if (isset($this->option[$option]))
		{
			return $this->option[$option];
		}
		return null;
	}
	
	public function addOption($option, $value)
	{
		$db = Database::Get();
		$stmt = $db->prepare('INSERT INTO `oj_contest_options` (`cid`,`key`,`value`) VALUES (?,?,?)');
		$stmt->execute(array($this->id,$option,$value));
	}
	
	public function displayRanking()
	{
		return $this->status >= self::STATUS_INPROGRESS && intval($this->getOption('display_ranking')) && ($this->status == self::STATUS_JUDGED || intval($this->getOption('display_preliminary_ranking')) );
	}
	
	public function generateRanking()
	{
		$suffix = 'FROM `oj_contest_submissions` LEFT JOIN `oj_records` ON (`oj_records`.`id`=`rid`) WHERE `rid` IS NOT NULL AND `oj_contest_submissions`.`cid` = `oj_contest_register`.`cid` AND `oj_contest_submissions`.`uid`=`oj_contest_register`.`uid`';
		$suffix_right = 'AND `status` = '.JudgeRecord::STATUS_ACCEPTED;
		$param_sql = array
		(
			'num_right' => "(SELECT count(*) {$suffix} {$suffix_right}) AS `num_right`,",
			'num_wrong' => "(SELECT count(*) {$suffix} AND `status` <> 0 AND `status` <> 2) AS `num_wrong`,",
			'duration' => "`finished`-`started` AS `duration`,",
			'total_time' => "(SELECT sum(`oj_contest_submissions`.`timestamp`-`started`) {$suffix} {$suffix_right}) AS `total_time`,",
			'max_time' => "(SELECT MAX(`oj_contest_submissions`.`timestamp`)-`started` {$suffix} {$suffix_right}) AS `max_time`,",
			'total_score' => "(SELECT sum(`score`) {$suffix}) AS `total_score`,",
		);
		
		$criteria = $this->getOption('ranking_criteria');
		
		$sql = 'SELECT ';
		
		$usedParams = array();
		
		foreach ($param_sql as $k => $v)
		{
			if (strpos($criteria,$k) !== false)
			{
				$sql .= $v;
				$usedParams[] = $k;
			}
		}
		$sql .= '`oj_contest_register`.`uid` AS `id`,`oj_users`.`username` AS `username` FROM `oj_contest_register` LEFT JOIN `oj_users` ON (`oj_users`.`id`=`oj_contest_register`.`uid`) WHERE `cid` = '.intval($this->id);
		
		$users = array();
		
		$db = Database::Get();
		$stmt = $db->query($sql);
		foreach ($stmt as $row)
		{
			$n = new ContestParticipant($row['id']);
			$n->username = $row['username'];
			
			foreach ($usedParams as $k)
			{
				$n->rankingParams[$k] = intval($row[$k]);
			}
			$users[] = $n;
		}
		
		$criteria = explode(';',$criteria);
		
		$asc = array();
		
		foreach ($criteria as $ci => $c)
		{
			if ($c[0] == 'd')
			{
				$asc[] = -1;
			}
			else
			{
				$asc[] = 1;
			}
			$c = substr($c,1);
			
			foreach ($users as $u)
			{
				
				foreach ($usedParams as $k)
				{
					$u->rankingCriteria[$ci] = str_replace($k,intval($u->rankingParams[$k]),$c);
				}
				$u->rankingCriteria[$ci] = eval('return ('.$u->rankingCriteria[$ci].');');
			}
			
		}
		
		usort($users,function($a,$b) use ($asc){
			foreach ($asc as $k => $v)
			{
				$diff = $v*($a->rankingCriteria[$k]-$b->rankingCriteria[$k]);
				if ($diff != 0)
				{
					return $diff;
				}
			}
			return $a->id-$b->id;
		});
		
		foreach ($users as $k => $v)
		{
			$v->rank = $k+1;
		}
		
		return $users;
	}
	
	public function judge()
	{
		if ($this->getOption('after_submit') == 'save')
		{
			$db = Database::Get();
			
			$db->exec("CALL contest_judge({$this->id})");
			
			JudgeRecord::PopAllWaitlist();
			
			$this->status = self::STATUS_JUDGING;
			$this->submit();
		}
		return false;
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
	
	/**
	 * Obtain the user-specific deadline, which is start time + duration or end time, whichever is earlier
	 * To faciliate changing duration or deadline during contest, this result is not cached
	 *
	 * @param User $user User
	 * @return mixed deadline, or false when user did not register or start.
	 */
	public function userDeadline($user)
	{
		$start = $this->userStart($user);
		
		if ($start === false)
		{
			return false;
		}
		
		if (!isset($this->duration) || !isset($this->endTime))
		{
			$this->fetch(array('duration','endTime'),NULL);
		}
		return min($start + $this->duration,$this->endTime);
	}
	
	/**
	 * Set user to started working
	 *
	 * @param User $user User Object
	 * @param int $time Time
	 */
	public function userStart($user,$time)
	{
		if ($user->id > 0)
		{
			$db = Database::Get();
			$db->query('UPDATE `oj_contest_register` SET `started` = '.$time.' WHERE `cid` = '.$this->id.' AND `uid` = '.$user->id);
			
			IO::SetSession('contest-began-'.$this->id,$time);
		}
	}
	
	public function submitSolution($problem, $user, $code, $lang)
	{
		switch ($this->getOption('after_submit'))
		{
		case 'judge':
			$rec = new JudgeRecord;
			$rec->problem = new Problem($problem);
			$rec->code = $code;
			$rec->lang = $lang;
			$rec->user = $user;
			$rec->add();
			
			$rec->dispatch($servers = $this->getOption('judge_servers') ? array_map(explode(',',$servers),function($c){return new JudgeServer(intval($c));}) : null);
			
		case  'save':
			$db = Database::Get();
			$stmt = $db->prepare("INSERT INTO `oj_contest_submissions` (cid,uid,pid,code,lang,timestamp,rid) VALUES (?,?,?,?,?,?,".(isset($rec) ? $rec->id : 'NULL').")");
			$stmt->execute(array($this->id,$user->id,$problem->id,$code,$lang,time()));
			break;
		}
	}
}
?>