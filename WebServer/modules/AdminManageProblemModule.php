<?php
defined('IN_OIOJ') || die('Forbidden');

import('Problem');
import('Cronjob');
import('SmartyForm');
class AdminManageProblemModule
{
	const UPLOADED_IMAGE_DIR = 'uploads/problem_figures/';
	
	public function run()
	{
		$user = User::GetCurrent();
		if (!($user->ableTo('add_problem') || ($user->ableTo('admin_cp') && !$user->unableTo('add_problem'))))
		{
			throw new PermissionException();
		}
		
		switch (IO::GET('act'))
		{
		case 'add':
			if (IO::GET('submit'))
			{
				$this->addProblemSubmit();
			}
			else
			{
				$this->displayProblemForm($this->generateProblemForm());
			}
			
			break;
		case 'edit':
			$this->editProblem();
			break;
		case 'uploadimage':
			$this->uploadImage();
			break;
		}
		
	}
	
	public function generateProblemForm(ActiveRecord $record = null)
	{
		$form = new SmartyForm('problem', 'index.php?mod=admin_problem&act=add&submit=1');
		
		$form->add(new SF_Checkbox('id','listing','label','Public'));
		
		$form->add(new SF_TextField('title'));
		$form->add(new SF_TextArea('body'));
		
		$form->add(new SF_TextField('id','input_file','label','Input'));
		$form->add(new SF_TextField('id','output_file','label','Output'));
		
		$form->add(new SF_Select('id','type','label','Type','options',array('Traditional' => '1', 'Interactive' => '2', 'Output' => '3')));
		$form->add(new SF_Select('id','comp_method','label','Compare','options',array('Full Text'=>'/FULLTEXT/','Omit Space at EOL'=>'/OMITSPACE/','Special Judge' => 'special')));
		$form->add(new SF_TextField('id','special_judge','label','bin'));
		
		if ($record)
		{
			$form->addRecord('problem', $record);
			$form->bind('title','problem');
			$form->bind('body','problem');
			$form->bind('input_file','problem','input');
			$form->bind('output_file','problem','output');
			$form->bind('type','problem');
			
			$form->bindByFunc('problem',function($form,$obj){
				if (substr($obj->compare,0,1) == '/')
				{
					$form->get('comp_method')->data = $obj->compare;
				}else
				{
					$form->get('comp_method')->data = 'special';
					$form->get('special_judge')->data = $obj->compare;
				}
			},function($form,$obj){
				$obj->compare = $form->get('comp_method')->data;
				if ($obj->compare == 'special')
				{
					$obj->compare = $form->get('special_judge')->data;
				}
			});
		}
		return $form;
	}
	
	public function editProblem()
	{
		$id = IO::GET('id',0,'intval');
		
		$obj = new Problem($id);
		$obj->fetch(array('id','title','body','input','output','compare','listing'));
		
		$form = $this->generateProblemForm($obj);
		
		$form->gatherFromRecord();
		$this->displayProblemForm($form);
	}
	
	public function addProblemSubmit()
	{
		// Check permission
		if (!User::GetCurrent()->ableTo('add_problem'))
		{
			throw new Exception('denied');
		}
		
		$prob = new Problem();
		$prob->user = User::GetCurrent();
		$prob->dispatched = 0;
		
		$form = $this->generateProblemForm($prob);
		$form->saveToRecord();
		/*
		$prob->title = IO::POST('title');
		$prob->body = IO::POST('body');
		$prob->type = IO::POST('type');
		$prob->listing = IO::POST('listing',0,function($data){return 1;});
		
		$prob->input = IO::POST('input_file');
		$prob->output = IO::POST('output_file');
		
		*/
		$inputs = IO::POST('case-in');
		$outputs = IO::POST('case-out');
		$tls = IO::POST('case-tl');
		$mls = IO::POST('case-ml');
		$scores = IO::POST('case-score');
		
		// Check if archive valid
		echo "Checking if archive file is legal...<br />\n";
		$zip = new ZipArchive();
		
		$legalFiles = array();
		
		if (is_uploaded_file($_FILES['archive']['tmp_name']))
		{
			$zip->open($_FILES['archive']['tmp_name']);
			foreach ($scores as $k => $v)
			{
				if (($idx1 = $zip->locateName($inputs[$k],ZIPARCHIVE::FL_NODIR)) === false || ($idx2 = $zip->locateName($outputs[$k],ZIPARCHIVE::FL_NODIR)) === false)
				{
					throw new Exception('wrong archive');
				}
				$legalFiles[$idx1] = $legalFiles[$idx2] = true;
			}
			$zip->close();
		}else{
			throw new Exception('error reading archive');
		}
		
		foreach ($scores as $k => $v)
		{
			$c = new TestCase();
			$c->cid = $k+1;
			$c->input = $inputs[$k];
			$c->answer = $outputs[$k];
			$c->timelimit = $tls[$k];
			$c->memorylimit = $mls[$k];
			$c->score = $v;
			$c->problem = $prob;
			
			$prob->testCases[] = $c;
		}
		
		
		$prob->add();
		
		$newloc = Settings::Get('tmp_dir').DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.$prob->id.'.zip';
		
		move_uploaded_file($_FILES['archive']['tmp_name'],$newloc);
		
		$prob->archiveLocation = $newloc;
		
		$db = Database::Get();
		
		echo 'Adding problem to dispatch queue<br />';
		
		$prob->queueForDispatch();
		
		Cronjob::AddJob('ProblemDistribution','dispatch',array(), 0, 3);
		
		echo 'done. Problem ID: '.$prob->id;

	}
	
	public function displayProblemForm($form)
	{
		OIOJ::$template->assign('sf_problem',$form);
		OIOJ::$template->display('admin_addproblem.tpl');
	}
	
	public function uploadImage()
	{
		$funcNum = IO::GET('CKEditorFuncNum',null);
		if ($funcNum === null)
		{
			die();
		}
		$message = '';
		$url = '';
		/*
		$CKEditor = IO::GET('CKEditor');
		$langCode = IO::GET('langCode');
		*/
		if (is_uploaded_file($_FILES['upload']['tmp_name']))
		{
			// Check file extension
			$validImages = array('jpg','jpeg','gif','png','jp2','bmp');
			$ext = strtolower(pathinfo($_FILES['upload']['name'],PATHINFO_EXTENSION));
			if (!in_array($ext, $validImages))
			{
				$message = 'Invalid image format';
			}else
			{
				$url = self::UPLOADED_IMAGE_DIR . time() .'-'.rand(1000,9999). '.' . $ext;
				move_uploaded_file($_FILES['upload']['tmp_name'],ROOT.$url);
			}
		}
		
		echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
	}
}
?>
