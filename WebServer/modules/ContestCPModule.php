<?php
defined('IN_OIOJ') || die('Forbidden');

import('Contest');
import('Problem');
import('JudgeRecord');

class ContestCPModule
{
	private $numberParticipants;
	private $numberParticipantsStarted;
	private $numberParticipantsWithSubmission;
	
	private $numberSubmissions;
	private $contest;
	private $cid;
	
	public function startContest()
	{
		$this->contest->startContest();
		Cronjob::RemoveJob("`class` = 'Contest' AND `method` = 'start' AND `reference` = {$this->contest->id}");
	}
	
	public function endContest()
	{
		$this->contest->endContest();
		Cronjob::RemoveJob("`class` = 'Contest' AND `method` = 'end' AND `reference` = {$this->contest->id}");
	}
	
	public function judge()
	{
		$this->contest->judge();
		Cronjob::RemoveJob("`class` = 'Contest' AND `method` = 'judge' AND `reference` = {$this->contest->id}");
	}
	
	public function finishJudge()
	{
		$this->contest->finishJudge();
	}
	
	public function run()
	{
		$this->cid = IO::GET('id',0,'intval');
		
		$this->contest = Contest::first(array('title','status','user'),NULL,$this->cid);
		
		$user = User::GetCurrent();
		
		if (!($user->id == $this->contest->id || !$user->ableTo('contestcp') || ($user->ableTo('admin_cp') && !$user->unableTo('contestcp'))))
		{
			throw new PermissionException();
		}
		
		if (!$this->contest)
		{
			throw new Exception('Invalid Contest');
		}
		
		switch (IO::GET('act'))
		{
		case 'start':
			$this->startContest();
			break;
		case 'end':
			$this->endContest();
			break;
		case 'judge':
			$this->judge();
			break;
		default:
			
		}
		
		$db = Database::Get();
		
		
		// General Statistics
		$stmt = $db->query('SELECT count(*) FROM `oj_contest_register` WHERE `cid` = '.$this->cid);
		$row = $stmt->fetch();
		$this->numberParticipants = $row[0];
		
		
		$stmt = $db->query('SELECT count(*) FROM `oj_contest_register`  WHERE `cid` = '.$this->cid .' AND `started` IS NOT NULL');
		$row = $stmt->fetch();
		$this->numberParticipantsStarted = $row[0];
		
		
		$stmt = $db->query('SELECT count(*) FROM `oj_contest_register` WHERE `cid` = '.$this->cid .' AND EXISTS (SELECT * FROM `oj_contest_submissions` WHERE `oj_contest_submissions`.`uid` = `oj_contest_register`.`uid` AND `oj_contest_submissions`.`cid` = `oj_contest_register`.`cid`)');
		$row = $stmt->fetch();
		$this->numberParticipantsWithSubmission = $row[0];
		
		
		$stmt = $db->query('SELECT count(*) FROM `oj_contest_submissions` WHERE `cid` = '.$this->cid);
		$row = $stmt->fetch();
		$this->numberSubmissions = $row[0];
		
		OIOJ::$template->assign('contest',$this->contest);
		OIOJ::$template->assign('status_code',$this->contest->status);
		OIOJ::$template->assign('num_participants',$this->numberParticipants);
		OIOJ::$template->assign('num_participants_wsub',$this->numberParticipantsWithSubmission);
		OIOJ::$template->assign('num_participants_started',$this->numberParticipantsStarted);
		OIOJ::$template->assign('num_submissions',$this->numberSubmissions);
		
		if ($this->contest->status == Contest::STATUS_JUDGING)
		{
			$result = $db->query('SELECT count(*) FROM `oj_contest_submissions` LEFT JOIN `oj_records` ON (`rid` = `oj_records`.`id`) WHERE `status` > '.JudgeRecord::STATUS_DISPATCHED.' AND `cid` = '.$this->cid)->fetch();
			OIOJ::$template->assign('num_judged',$result[0]);
			$result = $db->query('SELECT count(*) FROM `oj_contest_submissions` WHERE `rid` IS NOT NULL AND `cid` = '.$this->cid)->fetch();
			OIOJ::$template->assign('num_tojudge',$result[0]);
		}
		
		if ($this->contest->status == Contest::STATUS_JUDGED || ($this->contest->status >= Contest::STATUS_INPROGRESS && $this->contest->getOption('after_submit') == 'judge'))
		{
			OIOJ::$template->assign('analysis',$this->analysis());
		}
		
		OIOJ::$template->display('contestcp.tpl');
	}
	
	private function analysis()
	{
		return Database::Get()->query("SELECT `pid`,`title`,(SELECT COUNT(DISTINCT `uid`) FROM `oj_contest_submissions` WHERE `cid` = `oj_contest_problems`.`cid` AND `pid` = `oj_contest_problems`.`pid`) AS `users`,(SELECT AVG(`score`) FROM `oj_contest_submissions` LEFT JOIN `oj_records` ON (`oj_records`.`id` = `rid`) WHERE `cid` = `oj_contest_problems`.`cid` AND `oj_contest_submissions`.`pid` = `oj_contest_problems`.`pid` AND `rid` IS NOT NULL) AS `average`,(SELECT MAX(`score`) FROM `oj_contest_submissions` LEFT JOIN `oj_records` ON (`oj_records`.`id` = `rid`) WHERE `cid` = `oj_contest_problems`.`cid` AND `oj_contest_submissions`.`pid` = `oj_contest_problems`.`pid` AND `rid` IS NOT NULL) AS `maximum` FROM `oj_contest_problems` LEFT JOIN `oj_problems` ON (`pid` = `oj_problems`.`id`) WHERE `cid` = {$this->cid}")->fetchAll();
	}
}
?>