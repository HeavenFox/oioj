<?php
defined('IN_OIOJ') || die('Forbidden');

import('JudgeRecord');

class RecordsModule
{
	private $record;
	public function showSingleRecordAjax()
	{
		$this->setSingleRecordVars();
		echo json_encode(array('finished' => !in_array($this->record->status,array(JudgeRecord::STATUS_WAITING,JudgeRecord::STATUS_DISPATCHED)),'content' => OIOJ::$template->fetch('boxes/records_single.tpl')));
	}
	
	public function showSingleRecord()
	{
		$this->setSingleRecordVars();
		OIOJ::$template->display(IO::GET('popup') ? 'records_single_popup.tpl' : 'records_single.tpl');
	}
	
	private function setSingleRecordVars()
	{
		$this->record = $record = $this->getSingleRecord();
		OIOJ::$template->assign('id',$record->id);
		OIOJ::$template->assign('server_name',$record->server->name ? $record->server->name : 'None');
		OIOJ::$template->assign('status',$record->getReadableStatus());
		$resultsAvailable = in_array($record->status,array(JudgeRecord::STATUS_ACCEPTED,JudgeRecord::STATUS_CE,JudgeRecord::STATUS_REJECTED));
		OIOJ::$template->assign('results_available',$resultsAvailable);
		if ($resultsAvailable)
		{
			OIOJ::$template->assign('score',$record->score);
			$cases = $record->cases;
			foreach ($cases as $k=>$v)
			{
				$cases[$k] = $this->formatCaseResult($v);
			}
			OIOJ::$template->assign('cases',$cases);
		}
	}
	
	private function getSingleRecord()
	{
		return JudgeRecord::first(array('id','status','cases','score'),array('server' => array('name')),IO::GET('id',0,'intval'));
	}
	
	public function formatCaseResult($li)
	{
		$caseStr = array('Unknown','Time Limit Exceeded','Memory Limit Exceeded','Output Limit Exceeded','Forbidden System Call','Runtime Error','Wrong Answer','OK');
		
		$prefix = 'Case '.$li['CaseID'].': '.$caseStr[intval($li['CaseResult'])].'.';
						$timeMemory = ' Time: '.$li['CaseTime'].'s Memory: '.$li['CaseMemory'].'MB';
						$str = '';
						switch(intval($li['CaseResult']))
						{
							case 4:
								$str = $prefix . ' Call Code: '.$li['CaseExtendedCode'].' '.$timeMemory;
								break;
							case 5:
								$str = $prefix . ' Error Code: '.$li['CaseExtendedCode'] . ' ' . $timeMemory£»;
								break;
							default:
								$str = $prefix . $timeMemory;
						}
		return $str;
	}
	
	public function run()
	{
		if (IO::GET('id'))
		{
			if (IO::GET('ajax'))
			{
				$this->showSingleRecordAjax();
			}else
			{
				$this->showSingleRecord();
			}
		}else
		{
			//$db = Database::Get();
			
			$records = JudgeRecord::find(array('id','status','cases','lang','timestamp','score'),array('problem' => array('id','title'),'user' => array('id','username'),'server' => array('name')));
			
			//$stmt = $db->prepare('SELECT `oj_records`.`id` AS `id`,`pid`,`oj_problems`.`title` AS `prob_title`,`oj_records`.`uid`,`oj_users`.`username` AS `username`,`status`,`lang`,`server`,`oj_judgeservers`.`name` AS `server_name`, `cases`, `timestamp` FROM `oj_records` LEFT JOIN `oj_judgeservers` ON (`oj_judgeservers`.`id` = `server`) LEFT JOIN `oj_problems` ON (`pid` = `oj_problems`.`id`) LEFT JOIN `oj_users` ON (`oj_records`.`uid` = `oj_users`.`id`) LIMIT 0,50');
			//$stmt->execute();
			//$stmt->setFetchMode(PDO::FETCH_ASSOC);
			//$records = $stmt->fetchAll();
			
			$statusStr = array('Waiting','Dispatched','Accepted','Compile Error','Rejected');
			
			foreach($records as $k => $v)
			{
				$statusClass = '';
				switch($v->status)
				{
					case JudgeRecord::STATUS_WAITING:
					case JudgeRecord::STATUS_DISPATCHED:
						$statusClass = 'status-pending';
						break;
					case JudgeRecord::STATUS_ACCEPTED:
						$statusClass = 'status-ok';
						break;
					case JudgeRecord::STATUS_CE:
					case JudgeRecord::STATUS_REJECTED:
						$statusClass = 'status-wa';
						break;
				}
				$v->statusClass = $statusClass;
				$v->status = $statusStr[intval($v->status)];
				$detailList = array();
				//$score = 0;
				if ($v->cases)
				{
					foreach ($v->cases as $li)
					{
						//$score += intval($li['CaseScore']);
						
						$detailList[] = $this->formatCaseResult($li);
					}
				}
				$v->cases = $detailList;
			}
			
			OIOJ::$template->assign('records',$records);
			OIOJ::$template->display('records.tpl');
		}
	}
}
?>