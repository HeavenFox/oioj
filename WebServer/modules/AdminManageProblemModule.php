<?php
defined('IN_OIOJ') || die('Forbidden');

import('Problem');
import('Cronjob');
import('SmartyForm');
class AdminManageProblemModule
{
	const UPLOADED_IMAGE_DIR = 'uploads/problem_figures/';
	const UPLOADED_ATTACHMENTS_DIR = 'uploads/problem_attachments/';
	
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
			$this->displayProblemForm($this->generateProblemForm());
			break;
		case 'edit':
			$this->editProblem();
			break;
		case 'submit':
			$this->problemSubmit();
			break;
		case 'uploadimage':
			$this->uploadImage();
			break;
		case 'uploadattachments':
			$this->uploadAttachments();
			break;
		case 'removetag':
			$this->removeTag();
			break;
		case 'addtag':
			$this->addTag();
			break;
		}
		
	}
	
	public function removeTag()
	{
		User::GetCurrent()->assertAble('edit_tags');
		Database::Get()->exec('DELETE FROM `oj_problem_tags` WHERE `tid` = '.IO::GET('tid',0,'intval').' AND `pid` = '.IO::GET('pid',0,'intval'));
	}
	
	public function addTag()
	{
		User::GetCurrent()->assertAble('edit_tags');
		$problem = new Problem(IO::POST('pid',0,'intval'));
		if ($tid = IO::POST('tid',0,'intval'))
		{
			$problem->addTags($tid);
			
			echo json_encode(array('tid' => $tid));
		}else
		{
			$tag = Tag::AddIfNotExist(IO::POST('tag'));
			$problem->addTags($tag);
			
			echo json_encode(array('tid' => $tag->id));
		}
	}
	
	public function generateProblemForm(ActiveRecord $record = null)
	{
		$form = new SmartyForm('problem', 'index.php?mod=admin_problem&act=submit');
		
		$form->add(new SF_Hidden('id','id'));
		$form->add(new SF_Hidden('id','editing'));
		
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
			$form->bind('listing','problem');
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
			
			$form->bindByFunc('problem',function($form,$obj){
				$form->get('id')->data = $obj->id;	
			},function($form,$obj){
				if ($data = $form->get('id')->data)
				{
					$obj->id = $data;
				}
			});
		}
		return $form;
	}
	
	public function editProblem()
	{
		$id = IO::GET('id',0,'intval');
		
		$obj = Problem::first(array('id','title','body','input','output','compare','listing'), 'WHERE `id` = '.$id);
		
		$form = $this->generateProblemForm($obj);
		$form->get('id')->data = $id;
		$form->get('editing')->data = 1;
		
		$form->gatherFromRecord();
		$this->displayProblemForm($form);
	}
	
	public function problemSubmit()
	{
		// Check permission
		if (!User::GetCurrent()->ableTo('add_problem'))
		{
			throw new Exception('denied');
		}
		
		$prob = new Problem();
		
		$form = $this->generateProblemForm($prob);
		
		$form->gatherFromPOST();
		$form->saveToRecord();
		
		if (!$form->get('editing')->data)
		{
			$prob->user = User::GetCurrent();
			$prob->dispatched = 0;
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
			
			//---------------------------------
			// Test Cases
			//---------------------------------
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
			
			$newloc = Settings::Get('data_archive_dir').DIRECTORY_SEPARATOR.$prob->id.'.zip';
			
			move_uploaded_file($_FILES['archive']['tmp_name'],$newloc);
			
			$prob->archiveLocation = $newloc;
			
			//---------------------------------
			// Attachments
			//---------------------------------
			$attachFileNames = IO::POST('attach_filename');
			$attachStoredNames = IO::POST('attach_storedname');
			
			if ($attachFileNames)
			{
				
				foreach ($attachFileNames as $k => $v)
				{
					$attach = new ProblemAttachment();
					$attach->problem = $prob;
					$attach->storedname = $attachStoredNames[$k];
					$attach->filename = $v;
					$attach->add();
				}
			}
			
			//---------------------------------
			// Tags
			//---------------------------------
			$tags = array();
			$tag_ids = IO::POST('tag_tid');
			$tag_tag = IO::POST('tag_tag');
			for ($i=0;$i < count($tag_ids);$i++)
			{
				if ($tid = intval($tag_ids[$i]))
				{
					$tags[] = $tid;
				}else
				{
					$tags[] = $tag_tag[$i];
				}
			}
			$prob->addTags($tags);
			
			
			
			$prob->queueForDispatch();
			
			Cronjob::AddJob('ProblemDistribution','dispatch',array(), 0, 3);
			OIOJ::Redirect('Problem #'.$prob->id.' Saved Successfully. It has been added to distribution queue and shall appear once its data finishes propagating','index.php?mod=problem&id='.$prob->id);
		
		}
		else
		{
			$prob->update();
			OIOJ::Redirect('Problem saved Successfully. Directing you to problem...','index.php?mod=problem&id='.$prob->id);
		}
		
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
	
	public function uploadAttachments()
	{
		$attachments = array();
		
		for ($i = 0; $i < count($_FILES['attach']['name']);$i++)
		{
			if (is_uploaded_file($_FILES['attach']['tmp_name'][$i]))
			{
				$storedName = time() . '-' . rand(1000,9999);
				move_uploaded_file($_FILES['attach']['tmp_name'][$i],ROOT . self::UPLOADED_ATTACHMENTS_DIR . $storedName);
				$fileName = $_FILES['attach']['name'][$i];
				$attachments[] = array('storedName' => $storedName, 'fileName' => $fileName);
			}
		}
		
		echo json_encode($attachments);
	}
}
?>
