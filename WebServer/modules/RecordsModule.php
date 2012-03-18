<?php
defined('IN_OIOJ') || die('Forbidden');

import('JudgeRecord');
import('RecordSelector');

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
		
		if (!$record)
		{
			throw new InputException('Invalid record');
		}
		
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
		
		if ($record->status == JudgeRecord::STATUS_WAITING)
		{
			OIOJ::$template->assign('numwaiting',JudgeRecord::first(array('count'),'WHERE `timestamp` <= '.$record->timestamp)->count);
		}
		else if ($record->status == JudgeRecord::STATUS_DISPATCHED)
		{
			OIOJ::$template->assign('numsharing',JudgeRecord::first(array('count'),'WHERE `status` = '.JudgeRecord::STATUS_DISPATCHED.' AND `server` = '.$record->server->id)->count);
		}
	}
	
	private function getSingleRecord()
	{
		return JudgeRecord::first(array('id','status','cases','score','server' => array('id','name'),'timestamp'),IO::GET('id',0,'intval'));
	}
	
	public function formatCaseResult($li)
	{
		$caseStr = array('Unknown','Time Limit Exceeded','Memory Limit Exceeded','Output Limit Exceeded','Forbidden System Call','Runtime Error','Wrong Answer','OK');
		
		$str = 'Case '.$li['id'].': '.$caseStr[$li['result']].'.'.' Score: '.$li['score'] . (isset($li['detail']) ? (' Detail: '.$li['detail']):'') . ' Time: '.$li['time'].'s Memory: '.$li['memory'].'MB';
		
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
		
		$perPage = IO::GET('perpage', 20, 'intval');
		$pageNum = IO::REQUEST('page', 1, 'intval');
		$maxPage = 1;
		
		$selector = new RecordSelector('JudgeRecord');
			$records = $selector->findAtPage($pageNum, $perPage, $maxPage, array('id','status','cases','lang','timestamp','score','problem' => array('id','title'),'user' => array('id','username'),'server' => array('name')),'ORDER BY `timestamp` DESC');
			
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
			OIOJ::$template->assign('page_cur',$pageNum);
		OIOJ::$template->assign('page_max',$maxPage);
			OIOJ::$template->assign('records',$records);
			OIOJ::$template->display('records.tpl');
	}
}
?>