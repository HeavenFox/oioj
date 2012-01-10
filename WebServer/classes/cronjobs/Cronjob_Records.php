<?php
import('JudgeRecord');
/**
 * Cron Jobs: Records
 * @jobname Records
 * @jobdesc Dispatch Records in Waitlist
 */
class Cronjob_Contest extends Cronjob
{
	public function dispatch()
	{
		JudgeRecord::PopAllWaitlist();
	}
}
?>