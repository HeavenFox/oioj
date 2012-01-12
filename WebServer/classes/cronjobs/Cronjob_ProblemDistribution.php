<?php
import('Problem');
import('Cronjob');
/**
 * Cron Jobs: Records
 * @jobname Records
 * @jobdesc Dispatch Records in Waitlist
 */
class Cronjob_ProblemDistribution extends Cronjob
{
	public function dispatch()
	{
		fprintf(STDOUT,'dispatching..');
		$db = Database::Get();
		$problems = array();
		$problems_server = array();
		
		$servers = JudgeServer::find(array('id','name','ip','port','ftpUsername','ftpPassword'),null,'WHERE `online` = 1');
		
		$toRemove = array();
		
		$failureExists = false;
		
		foreach ($db->query('SELECT `id`,`pid`,`server`,`file` FROM `oj_probdist_queue`') as $row)
		{
			$pid = intval($row['pid']);
			if (!isset($problems[$pid]))
			{
				$problems[$pid] = Problem::first(array('id','type','input','output','compare'),NULL,$pid);
				$problems[$pid]->getCases();
				$problems[$pid]->archiveLocation = $row['file'];
			}
			$sid = intval($row['server']);
			$problems_server[$pid][$sid] = true;
			$found = false;
			foreach ($servers as $server)
			{
				if ($server->id == $sid)
				{
					$found = true;
					try
					{
						$problems[$pid]->dispatch($server);
					}catch(Exception $e)
					{
						$failureExists = true;
						break;
					}
					
					$toRemove[] = intval($row['id']);
					unset($problems_server[$pid][$sid]);
					$this->log('Dispatched Problem '.$pid.' to server '.$sid);
					
					break;
				}
			}
			if ($found)
			{
				$failureExists = true;
			}
		}
		$toFinishDispatch = array();
		foreach ($problems_server as $k => $prob)
		{
			if (count($prob) == 0)
			{
				$toFinishDispatch[] = $k;
			}
		}
		
		if (count($toFinishDispatch))
		{
			$db->exec('UPDATE `oj_problems` SET `dispatched` = 1 WHERE `id` IN ('.implode(',',$toFinishDispatch).')');
		}
		
		if (count($toRemove))
		{
			$db->exec('DELETE FROM `oj_probdist_queue` WHERE `id` IN ('.implode(',',$toRemove).')');
		}
		
		// Retry sometime later
		if ($failureExists)
		{
			return array('next' => time() + 5*60);
		}
	}
}
?>