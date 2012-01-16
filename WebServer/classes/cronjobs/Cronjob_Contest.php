<?php
import('Contest');
/**
 * Cron Jobs: Contest
 * @jobname Contest
 * @jobdesc Process a scheduled contest
 */
class Cronjob_Contest extends Cronjob
{
	public function start()
	{
		$contest = new Contest($this->reference);
		
		$contest->startContest();
	}
	
	public function end()
	{
		$contest = new Contest($this->reference);
		
		$contest->endContest();
	}
	
	public function judge()
	{
		$contest = new Contest($this->reference);
		
		$contest->judge();
	}
}