<?php
defined('IN_OIOJ') || die('Forbidden');

import('Problem');
import('RecordSelector');

class ProblemListModule
{
	const MAX_PROBLEM_PER_PAGE = 50;
	const DEFAULT_PROBLEM_PER_PAGE = 20;
	
	public function run()
	{
		OIOJ::AddBreadcrumb('Problems');
		$probPerPage = IO::GET('perpage', self::DEFAULT_PROBLEM_PER_PAGE, 'intval');
		if ($probPerPage < 1 || $probPerPage > self::MAX_PROBLEM_PER_PAGE)
		{
			$probPerPage = self::DEFAULT_PROBLEM_PER_PAGE;
		}
		
		$pageNum = IO::REQUEST('page', 1, 'intval');
		
		$maxPage = 1;
		
		$selector = new RecordSelector('Problem');
		
		$problems = $selector->findAtPage($pageNum, $probPerPage, $maxPage, array('id','title','submission','accepted'), "WHERE `listing` > 0 AND `dispatched` > 0");
		
		OIOJ::$template->assign('problems',$problems);
		OIOJ::$template->assign('page_cur',$pageNum);
		OIOJ::$template->assign('page_max',$maxPage);
		
		OIOJ::$template->display('problemlist.tpl');
	}
}
?>