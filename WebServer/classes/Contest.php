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
		if (isset($this->option[$option]))
		{
			return $this->option[$option];
		}
		return null;
	}
	
	public function generateRanking()
	{
		$suffix = 'FROM `oj_contest_submissions` LEFT JOIN `oj_records` ON (`oj_records`.`id`=`rid`) WHERE `rid` IS NOT NULL AND `oj_contest_submissions`.`cid` = `oj_contest_register`.`cid` AND `oj_contest_submissions`.`uid`=`oj_contest_register`.`uid`';
		$suffix_right = 'AND `status` = 2';
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
		
		usort($users,function($a,$b) use ($criteria,$usedParams){
			$objs = array($a,$b);
			foreach ($criteria as $c)
			{
				$replacedc = array();
				$asc = 1;
				if ($c[0] == 'd')
				{
					$asc = -1;
				}
				$c = substr($asc,1);
				for ($i=0;$i<2;$i++)
				{
					foreach ($usedParams as $k)
					{
						$replacedc[$i] = str_replace($k,intval($objs[$i]->rankingParams[$k]),$c);
					}
					$replacedc[$i] = eval('return '.$replacedc[$i].';');
				}
				if (($diff = $asc*($replacedc[0]-$replacedc[1])) != 0)
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
		
		/*
		if (($this->getOption('after_submit') == 'judge' && $this->getOption('show_realtime_rank')) || $this->status == self::STATUS_JUDGED)
		{
			// Score, Num Right, Total Time, Num Wrong
			'SELECT FROM `oj_contest_register` ';
		}
		*/
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
			$rec->timestamp = time();
			$rec->submit();
			
			$rec->dispatch($servers = $this->getOption('judge_servers') ? array_map(explode(',',$servers),function($c){return new JudgeServer(intval($c));}) : null);
			
		case  'save':
			$db = Database::Get();
			$stmt = $db->prepare("INSERT INTO `oj_contest_submissions` (cid,uid,pid,code,lang,timestamp,rid) VALUES (?,?,?,?,?,?,".(isset($rec) ? $rec->id : 'NULL').")");
			$stmt->execute(array($this->id,$problem->id,$user->id,$code,$lang,time()));
			break;
		}
	}
}
?>