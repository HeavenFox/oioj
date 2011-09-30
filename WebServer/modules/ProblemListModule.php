<?php
import('Problem');

class ProblemListModule
{
	const MAX_PROBLEM_PER_PAGE = 50;
	const DEFAULT_PROBLEM_PER_PAGE = 20;
	public function run()
	{
		$probPerPage = intval($_GET['perpage']);
		if ($probPerPage < 1 || $probPerPage > self::MAX_PROBLEM_PER_PAGE)
		{
			$probPerPage = self::DEFAULT_PROBLEM_PER_PAGE;
		}
		
		$pageNum = intval($_GET['page']);
		if ($pageNum < 1)
		{
			$pageNum = 1;
		}
		$lowerLimit = ($pageNum-1)*$probPerPage;
		$problems = Problem::find(array('id','title'), null, "LIMIT {$lowerLimit},{$probPerPage}");
		
		OIOJ::$template->assign('problems',$problems);
		OIOJ::$template->display('problemlist.tpl');
	}
}
?>