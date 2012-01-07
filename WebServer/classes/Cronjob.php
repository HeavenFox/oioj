<?php
import('Settings');
class Cronjob
{
	/*
	static $tableName = 'oj_cronjobs';
	static $schema = array(
		'id' => array('class' => 'int'),
		'title' => array('class' => 'string'),
		'status' => array('class' => 'int'),
	*/
	
	public static function RunScheduled()
	{
		$lock = fopen(Settings::Get('tmp_dir').'/cron.lock','wb');
		if (flock($lock,LOCK_EX | LOCK_NB))
		{
			// Execute cron jobs
			$db = Database::Get();
			
			$stmt = $db->query('SELECT `id`,`class`,`parameter`, FROM `oj_cronjobs` WHERE `next` < '.time());
			
			foreach ($stmt as $row)
			{
				import('cronjobs.'.$row['class']);
				
				$job = new $row['class'];
				
				$result = $job->run(unserialize($row['arugments']));
				
				if (!$result || $result['next'] <= 0)
				{
					$query = 'DELETE FROM `oj_cronjobs` WHERE `id`='.$row['id'];
					
				}
				else
				{
					$query = 'UPDATE `oj_cronjobs` SET `next` = '.$result['next'];
					if (isset($result['arguments']))
					{
						$query .= ', `argument` = '.serialize($result['arguments']);
					}
					$query .= ' WHERE `id`='.$row['id'];
				}
			}
			
			flock($lock,LOCK_UN);
			fclose($lock);
		}
	}
	
}
?>