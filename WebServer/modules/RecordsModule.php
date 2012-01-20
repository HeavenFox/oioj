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
		return JudgeRecord::first(array('id','status','cases','score','server' => array('name')),IO::GET('id',0,'intval'));
	}
	
	public function formatCaseResult($li)
	{
		$caseStr = array('Unknown','Time Limit Exceeded','Memory Limit Exceeded','Output Limit Exceeded','Forbidden System Call','Runtime Error','Wrong Answer','OK');
		
		$prefix = 'Case '.$li['CaseID'].': '.$caseStr[intval($li['CaseResult'])].'.'.' Score: '.$li['CaseScore'];
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
			$this->listRecords();
		}
	}
	
	public function listRecords()
	{
		OIOJ::AddBreadcrumb('Records');
			$records = JudgeRecord::find(array('id','status','cases','lang','timestamp','score','problem' => array('id','title'),'user' => array('id','username'),'server' => array('name')));
			
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
				$v->statusString = $statusStr[$v->status];
				
				$detailList = array();
				
				if ($v->cases)
				{
					foreach ($v->cases as $li)
					{
						$detailList[] = $this->formatCaseResult($li);
					}
				}
				$v->cases = $detailList;
			}
			
			OIOJ::$template->assign('records',$records);
			OIOJ::$template->display('records.tpl');
	}
}
?>