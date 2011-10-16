<?php
import('JudgeRecord');

class RecordsModule
{
	public function showSingleRecordAjax()
	{
		$record = JudgeRecord::first(array('id'),null,'WHERE `id` = ?'.IO::GET('id',null,'intval'));
	}
	
	public function run()
	{
		OIOJ::InitDatabase();
		
		$db = Database::Get();
		
		$stmt = $db->prepare('SELECT `oj_records`.`id` AS `id`,`pid`,`oj_problems`.`title` AS `prob_title`,`oj_records`.`uid`,`oj_users`.`username` AS `username`,`status`,`lang`,`server`,`oj_judgeservers`.`name` AS `server_name`, `cases`, `timestamp` FROM `oj_records` LEFT JOIN `oj_judgeservers` ON (`oj_judgeservers`.`id` = `server`) LEFT JOIN `oj_problems` ON (`pid` = `oj_problems`.`id`) LEFT JOIN `oj_users` ON (`oj_records`.`uid` = `oj_users`.`id`) LIMIT 0,50');
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$records = $stmt->fetchAll();
		
		$statusStr = array('Waiting','Dispatched','Accepted','Compile Error','Rejected');
		$caseStr = array('Unknown','Time Limit Exceeded','Memory Limit Exceeded','Output Limit Exceeded','Forbidden System Call','Runtime Error','Wrong Answer','OK');
		
		
		foreach($records as $k => $v)
		{
			$statusClass = '';
			switch($v['status'])
			{
				case JudgeRecord::STATUS_WAITING:
				case JudgeRecord::STATUS_DISPATCHED:
					$statusClass = 'status_pending';
					break;
				case JudgeRecord::STATUS_ACCEPTED:
					$statusClass = 'status_ok';
					break;
				case JudgeRecord::STATUS_CE:
				case JudgeRecord::STATUS_REJECTED:
					$statusClass = 'status_wa';
					break;
			}
			$records[$k]['status_class'] = $statusClass;
			$records[$k]['status'] = $statusStr[intval($records[$k]['status'])];
			$details = unserialize($v['cases']);
			$detailList = array();
			$score = 0;
			if ($details)
			{
				foreach ($details as $li)
				{
					$li = parseProtocol($li);
					$score += intval($li['CaseScore']);
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
					$detailList[] = $str;
				}
			}
			$records[$k]['cases'] = $detailList;
			$records[$k]['score'] = $score;
		}
		
		OIOJ::$template->assign('records',$records);
		OIOJ::$template->display('records.tpl');
	}
}
?>