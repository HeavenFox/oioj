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
		if (IO::GET('act') == 'tagcomplete')
		{
			$tags = Problem::SearchTags('%'.IO::REQUEST('term').'%', 10);
			$result = array();
			foreach ($tags as $tag)
			{
				$n = array();
				$n['label'] = $tag->tag;
				$n['value'] = $tag->id;
				$result[] = $n;
			}
			die(json_encode($result));
		}
		
		OIOJ::AddBreadcrumb('Problems');
		
		$probPerPage = IO::GET('perpage', self::DEFAULT_PROBLEM_PER_PAGE, 'intval');
		
		if ($probPerPage < 1 || $probPerPage > self::MAX_PROBLEM_PER_PAGE)
		{
			$probPerPage = self::DEFAULT_PROBLEM_PER_PAGE;
		}
		
		$pageNum = IO::REQUEST('page', 1, 'intval');
		
		$maxPage = 1;
		
		$selector = new RecordSelector('Problem');
		
		if (IO::GET('tag'))
		{
			$problems = $selector->findAtPage($pageNum, $probPerPage, $maxPage, array('id','title','submission','accepted'), IO::GET('tag'), null, function($prop, $tagid, $suffix){return Problem::GetByTag($prop, $tagid, $suffix);});
		
		}
		else if (IO::GET('tagquery'))
		{
			User::GetCurrent()->assertNotUnable('query_tag_advanced');
			$problems = Problem::queryByTags(array('id','title','submission','accepted'),json_decode(IO::GET('tagquery')));
		}
		else if ($keyword = IO::POST('keyword'))
		{
			User::GetCurrent()->assertNotUnable('query_search');
			$problems = $selector->findAtPage($pageNum, $probPerPage, $maxPage, array('id','title','submission','accepted'), "WHERE `title` LIKE ? AND `listing` > 0 AND `dispatched` > 0", array('%'.$keyword.'%'));
		}
		else
		{
			$problems = $selector->findAtPage($pageNum, $probPerPage, $maxPage, array('id','title','submission','accepted'), "WHERE `listing` > 0 AND `dispatched` > 0");
		}
		
		OIOJ::$template->assign('problems',$problems);
		
		// Paging System
		OIOJ::$template->assign('page_cur',$pageNum);
		OIOJ::$template->assign('page_max',$maxPage);
		
		// Tags System
		OIOJ::$template->assign('tags',Problem::GetPopularTags(8));
		
		OIOJ::$template->display('problemlist.tpl');
	}
}
?>