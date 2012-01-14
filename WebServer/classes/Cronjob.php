<?php
import('ActiveRecord');
import('Settings');
class Cronjob extends ActiveRecord
{
	static $tableName = 'oj_cronjobs';
	static $schema = array(
		'id' => array('class' => 'int'),
		'class' => array('class' => 'string'),
		'method' => array('class' => 'string'),
		'arguments' => array('class' => 'string', 'setter' => 'serialize', 'getter' => 'unserialize'),
		'reference' => array('class' => 'int'),
		'next' => array('class' => 'timestamp'),
		'qos' => array('class' => 'int'),
		'enabled' => array('class' => 'bool')
	);
	
	public static function RunScheduled($qos)
	{
		$lock = fopen(Settings::Get('tmp_dir').'cronlock-'.$qos,'wb');
		if (flock($lock,LOCK_EX | LOCK_NB))
		{
			// Execute cron jobs
			$db = Database::Get();
			
			$stmt = $db->query('SELECT `id`,`class`,`method`,`arguments`,`reference` FROM `oj_cronjobs` WHERE `qos` = '.$qos.' AND `next` < '.time());
			
			foreach ($stmt as $row)
			{
				$clsName = 'Cronjob_'.$row['class'];
				import('cronjobs.'.$clsName);
				
				$job = new $clsName;
				$job->reference = intval($row['reference']);
				
				
				$args = unserialize($row['arguments']);
				if (!$args) $args = array();
				$result = call_user_func_array(array($job,$row['method']),$args);
				
				if (!$result || !isset($result['next']))
				{
					$query = 'DELETE FROM `oj_cronjobs` WHERE `id`='.$row['id'];
				}
				else
				{
					$query = 'UPDATE `oj_cronjobs` SET `next` = '.$result['next'];
					if (isset($result['arguments']))
					{
						$query .= ', `arguments` = '.serialize($result['arguments']);
					}
					$query .= ' WHERE `id`='.$row['id'];
				}
				$db->exec($query);
				
				flock($lock,LOCK_UN);
			}
			fclose($lock);
		}
	}
	
	/**
	 * Add a Cron Job
	 * @param string $cls Job class
	 * @param string $method Class method
	 * @param array $arguments Arguments for method
	 * @param int $next UNIX timestamp of next launch
	 * @param int $qos Quality of Service level
	 * @param int $reference Reference variable
	 */
	public static function AddJob($cls, $method, $arguments, $next, $qos, $reference = null)
	{
		$db = Database::Get();
		$stmt = $db->prepare('INSERT INTO `oj_cronjobs` (`class`,`method`,`arguments`,`next`,`qos`,`reference`) VALUES (?,?,?,?,?,?)');
		$stmt->execute(array($cls,$method,serialize($arguments),$next,$qos,$reference));
	}
	
	public function log($content)
	{
		$db = Database::Get();
		$stmt = $db->prepare('INSERT INTO `oj_cronjobs_log` (`class`,`content`,`timestamp`) VALUES (?,?,UNIX_TIMESTAMP())');
		$stmt->execute(array(substr(get_class($this),8),$content));
	}
}
?>