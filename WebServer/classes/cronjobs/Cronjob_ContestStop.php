<?php
/**
 * Cron Job: Contest Start
 * @jobname Contest Start
 * @jobdesc Start a scheduled contest
 */
class Cronjob_ContestStart extends Cronjob
{
	public function run($contest,$action)
	{
		$contest = unserialize($contest);
		if ($contest instanceof Contest)
		{
			
		}
		else
		{
			throw new Exception('invalid contest');
		}
	}
}