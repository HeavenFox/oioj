<?php
defined('IN_OIOJ') || die('Forbidden');

import('Problem');

class ProblemModule
{
	const UPLOADED_ATTACHMENTS_DIR = 'uploads/problem_attachments/';
	/**
	 * @var Problem
	 */
	protected $problem;
	public function run()
	{
		if (IO::GET('act') == 'attach')
		{
			$this->getAttachment();
			return;
		}
		$probID = IO::GET('id',0,'intval');
		
		if (!OIOJ::$template->isCached('problem.tpl', $probID))
		{
			if (!($obj = $this->loadProblem($probID)) || $obj->listing == 0) {
				throw new Exception('Problem does not exist', 404);
			}
		}
		$this->display($probID);
	}
	
	public function loadProblem($id)
	{
		$this->problem = Problem::first(array('id','title','body','accepted','submission','output','input','source','listing','user' => array('username')),$id);
		if (!$this->problem) return false;
		$this->problem->getComposite(array('attachments' => array('id','filename')));
		OIOJ::$template->assign('pid', $id);
		OIOJ::$template->assign('problem', $this->problem);
		return $this->problem;
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
	
	public function display($probID)
	{
		OIOJ::AddBreadcrumb(array('Problems' => 'index.php?mod=problemlist', $this->problem->title => ''));
		
		OIOJ::$template->display('problem.tpl',$probID);
	}
}
?>