<?php
defined('IN_OIOJ') || die('Forbidden');

import('Problem');

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
		
		$pageNum = IO::GET('page', 1, 'intval');
		if ($pageNum < 1)
		{
			$pageNum = 1;
		}
		$lowerLimit = ($pageNum-1)*$probPerPage;
		$problems = Problem::find(array('id','title','submission','accepted'), null, "WHERE `listing` > 0 LIMIT {$lowerLimit},{$probPerPage}");
		
		OIOJ::$template->assign('problems',$problems);
		OIOJ::$template->display('problemlist.tpl');
	}
}
?>