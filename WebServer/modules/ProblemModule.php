<?php
defined('IN_OIOJ') || die('Forbidden');

import('Problem');
import('ProblemComment');

class ProblemModule
{
	const UPLOADED_ATTACHMENTS_DIR = 'uploads/problem_attachments/';
	
	const DEFAULT_COMMENT_PERPAGE = 10;
	
	/**
	 * @var Problem
	 */
	protected $problem;
	public function run()
	{
		$probID = IO::GET('id',0,'intval');
		
		$this->checkPermission();
		if (!$this->checkCache($probID))
		{
			$this->loadProblem($probID);
			$this->checkProblemPermission();
		}
		
		switch (IO::GET('act'))
		{
		case 'attach':
			$this->getAttachment();
			break;
		case 'discussion':
			$this->discussion();
			break;
		case 'comments':
			echo $this->getComments();
			break;
		default:
			$this->display($probID);
		}
		
		
	}
	
	public function checkCache($probID)
	{
		return OIOJ::$template->isCached('problem.tpl', $probID);
	}
	
	public function checkPermission()
	{
		User::GetCurrent()->assertNotUnable('view_problem');
	}
	
	public function checkProblemPermission()
	{
		if (!$this->problem->listing && User::GetCurrent()->id != $this->problem->user->id && !User::GetCurrent()->ableTo('edit_problem'))
		{
			throw new InputException('Problem does not exist');
		}
	}
	
	public function loadProblem($id)
	{
		$this->problem = Problem::first(array('id','title','body','accepted','submission','output','input','source','listing','user' => array('id','username')),$id);
		if (!$this->problem) return false;
		$this->problem->getComposite(array('attachments' => array('id','filename')));
		$this->problem->getTags();
		OIOJ::$template->assign('problem', $this->problem);
		return $this->problem;
	}
	
	public function discussion()
	{
		if (IO::GET('do') == 'post')
		{
			import('filters.FilterHTML');
			import('filters.FilterSpoiler');
			$filter = new FilterSpoiler(new FilterHTML());
			
				$comment = new ProblemComment();
				$comment->content = $filter->sanitize(nl2br(IO::POST('content')));
				$comment->timestamp = time();
				User::GetCurrent()->fetch(array('email'));
				$comment->user = User::GetCurrent();
				$comment->problem = $this->problem;
				$comment->parentID = IO::POST('parent',0,'intval');
				$comment->add();
				
				OIOJ::$template->assign('comment',$comment);
				OIOJ::$template->display('boxes/comment.tpl');
		}
		else
		{
			OIOJ::$template->assign('html',$this->getComments());
			OIOJ::$template->display('problem_discussion.tpl');
		}
	}
	
	public function getAttachment()
	{
		$aid = IO::GET('aid',0,'intval');
		$attach = ProblemAttachment::first(array('filename','storedname'),'WHERE `id` = '.$aid);
		
		if (!$attach)
		{
			throw new Exception('Attachment missing');
		}
		
		$fp = fopen(ROOT. self::UPLOADED_ATTACHMENTS_DIR . $attach->storedname);
		
		header("Content-type: application/octet-stream"); 
		header('Content-Disposition: attachment; filename="'.$attach->filename.'"');
		
		fpassthru($fp);
	}
	
	public function getComments()
	{
		$page = IO::GET('page',1,'intval');
		$pid = IO::GET('id',0,'intval');
		$perPage = IO::GET('perpage',self::DEFAULT_COMMENT_PERPAGE,'intval');
		
		$this->problem->findComposite('comments', array('id','parentID','content','user' => array('id','username','email'),'timestamp'),'ORDER BY `timestamp` ASC');
		
		$allComments = $this->problem->comments;
		
		foreach ($allComments as $k => $v)
		{
			if ($v->parentID)
			{
				for ($i = 0; $i < $k; $i++)
				{
					if ($allComments[$i]->id == $v->parentID)
					{
						$allComments[$i]->children[] = $v;
						break;
					}
				}
			}
			
		}
		
		$commentsChunk = array_chunk(array_filter($allComments, function($v){
			return !$v->parentID;
		}),$perPage);
		
		$comments = count($commentsChunk) ? $commentsChunk[$page-1] : null;
		$maxPage = count($commentsChunk);
		
		function generateHTML($com, $level)
		{
			if (!$com || count($com) == 0)
			{
				return '';
			}
			$html = '';
			foreach ($com as $comment)
			{
				OIOJ::$template->assign('comment',$comment);
				$html .= OIOJ::$template->fetch('boxes/comment.tpl');
				$children = generateHTML($comment->children,$level+1);
				if (strlen($children) > 0)
				{
					$html .= "<div class='threaded_comment level{$level}'>";
					$html .= $children;
					$html .= '</div>';
				}
			}
			return $html;
		}
		if ($comments)
		{
			OIOJ::$template->assign('comments_html',generateHTML($comments,1));
		}
		
		OIOJ::$template->assign('curPage',$page);
		OIOJ::$template->assign('maxPage',$maxPage);
		
		return OIOJ::$template->fetch('boxes/problem_comments.tpl');
	}
	
	public function display($probID)
	{
		OIOJ::AddBreadcrumb(array('Problems' => 'index.php?mod=problemlist', $this->problem->title => ''));
		
		OIOJ::$template->display('problem.tpl',$probID);
	}
}
?>