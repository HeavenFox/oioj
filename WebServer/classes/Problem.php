<?php
import('TaggedRecord');
import('TestCase');
import('JudgeServer');
import('Settings');
import('ProblemAttachment');

class Problem extends TaggedRecord
{
	const TYPE_CLASSIC = 1;
	const TYPE_OUTPUT = 2;
	const TYPE_INTERACTIVE = 3;
	
	const IO_SCREEN = '/SCREEN/';
	
	const COMPARE_FULLTEXT = '/FULLTEXT/';
	const COMPARE_OMITSPACE = '/OMITSPACE/';
	
	public static $schema = array(
			'id' => array('class' => 'int'),
			'title' => array('class' => 'string'),
			'body' => array('class' => 'text'),
			'source' => array('class' => 'text'),
			'solution' => array('class' => 'text'),
			'type' => array('class' => 'int'),
			'input' => array('class' => 'string'),
			'output' => array('class' => 'string'),
			'compare' => array('class' => 'string'),
			'submission' => array('class' => 'int'),
			'accepted' => array('class' => 'int'),
			'listing' => array('class' => 'bool'),
			'dispatched' => array('class' => 'bool'),
			'user' => array('class' => 'User', 'comp' => 'one', 'column' => 'uid'),
			
			'attachments' => array('class' => 'ProblemAttachment', 'comp' => 'many', 'column' => 'pid'),
			
			'comments' => array('class' => 'ProblemComment', 'comp' => 'many', 'column' => 'pid'),
			
			'count' => array('class' => 'int', 'query' => 'count(`id`)')
		);
	public static $tableName = 'oj_problems';
	public static $keyProperty = 'id';
	
	public static $tagAssocTable = array('oj_problem_tags','pid','tid');
	
	public static $LanguageMap = array(
			'c' => 'c',
			'cpp' => 'cpp',
			'cc' => 'cpp',
			'cxx' => 'cpp',
			'pas' => 'pas'
		);
	
	public $testCases = array();
	public $archiveLocation;
	public $dependencies;
	
	const DATA_PREFIX = 'data';
	const DATA_INPUT_SUFFIX = '.in';
	const DATA_ANSWER_SUFFIX = '.out';
	const DATA_SCORE = 10;
	
	public function addCaseByContent($input, $output, $tl, $ml, $score = self::DATA_SCORE)
	{
		$i = count($this->testCases)+1;
		$inputName = self::DATA_PREFIX . strval($i) . self::DATA_INPUT_SUFFIX;
		$answerName = self::DATA_PREFIX . strval($i) . self::DATA_ANSWER_SUFFIX;
		
		$curCase = new TestCase();
		$curCase->cid = $i;
		$curCase->problem = $this;
		$curCase->input = $inputName;
		$curCase->answer = $answerName;
		$curCase->timelimit = $tl;
		$curCase->memorylimit = $ml;
		$curCase->score = $score;
		
		$curCase->inputContent = $input;
		$curCase->answerContent = $output;
		
		$this->testCases[] = $curCase;
	}
	
	public function getCases()
	{
		$this->testCases = TestCase::find(array('cid','input','answer','timelimit','memorylimit','score'),'WHERE `pid` = '.$this->id);
	}
	
	public function createArchive($location = null)
	{
		if (!$location)
		{
			//$location = tempnam(sys_get_temp_dir(),'PBA');
			$location = Settings::Get('tmp_dir'). DIRECTORY_SEPARATOR . 'cases' . DIRECTORY_SEPARATOR . $this->id . '.zip';
		}
		$this->archiveLocation = $location;
		$zip = new ZipArchive();
		if ($zip->open($location,ZipArchive::CREATE))
		{
			foreach ($this->testCases as $k => $v)
			{
				$zip->addFromString($v->input,str_replace("\r",'',$v->inputContent));
				$zip->addFromString($v->answer,str_replace("\r",'',$v->answerContent));
			}
			$zip->close();
			return true;
		}
		return false;
	}
	
	public function dispatch($server)
	{
		if (!$this->archiveLocation)
		{
			throw new Exception('Archive not created');
		}
		if ($server->isLocal())
		{
			copy($this->archiveLocation,Settings::Get('local_judgeserver_data_dir').$this->id.'.zip');
			//$this->archiveLocation = null;
		}
		else
		{
			// FTP
			$ftp = ftp_connect($server->ip);
			
			
			if (!$ftp || !ftp_login($ftp,$server->ftpUsername,$server->ftpPassword))
			{
				throw new Exception('Unable to connect FTP');
			}
			
			$file = fopen($this->archiveLocation,'rb');
			
			ftp_fput($ftp,$this->id.'.zip',$file,FTP_BINARY);
			
			fclose($file);
			
			ftp_close($ftp);
			
		}
		
		if (!$server->dispatch($this->generateDispatchString()))
		{
			throw new Exception('Schema');
		}
		
	}
	
	public function queueForDispatch()
	{
		$servers = JudgeServer::find(array('id'));
		
		if ($servers)
		{
			$insQuery = 'INSERT INTO `oj_probdist_queue` (`pid`,`server`,`file`) VALUES ';
			
			$first = true;
			foreach ($servers as $server)
			{
				if ($first)
				{
					$first = false;
				}else
				{
					$insQuery .= ',';
				}
				
				$insQuery .= "({$this->id},{$server->id},:archive)";
			}
			
			$stmt = Database::Get()->prepare($insQuery);
			$stmt->bindParam('archive',$this->archiveLocation);
			$stmt->execute();
		}
	}
	
	public function purge()
	{
		unlink($this->archiveLocation);
	}
	
	private function generateDispatchString()
	{
		$str .= strval(count($this->testCases)) . "\n";
		foreach ($this->testCases as $c)
		{
			$str .= "{$c->cid} {$c->input} {$c->answer} {$c->timelimit} {$c->memorylimit} {$c->score}\n";
		}
		// TODO add dependency support
		$str .= "0\n";
		$str .= "zip\n";
		
		$requestNode = new SimpleXMLElement('<request />');
		$requestNode->addAttribute('type','addproblem');
		$requestNode->addAttribute("token",Settings::Get("token"));
		if (strlen(Settings::Get('backup_token')) > 0)
		{
			$requestNode->addAttribute("backup_token",Settings::Get("backup_token"));
		}
		
		$requestNode->addAttribute("version","2.0");
		
		$problemNode = $requestNode->addChild('problem');
		$problemNode->addAttribute("id",$this->id);
		$problemNode->addAttribute('type',$this->type);
		$problemNode->addAttribute('compare',$this->compare);
		$problemNode->addAttribute('input',$this->input);
		$problemNode->addAttribute('output',$this->output);
		
		$casesNode = $requestNode->addChild('cases');
		
		foreach ($this->testCases as $c)
		{
			$cur = $casesNode->addChild('case');
			$cur->addAttribute('id', $c->cid);
			$cur->addAttribute('input', $c->input);
			$cur->addAttribute('answer', $c->answer);
			$cur->addAttribute('timelimit', $c->timelimit);
			$cur->addAttribute('memorylimit', $c->memorylimit);
			$cur->addAttribute('score', $c->score);
		}
		
		// TODO dependencies
		
		$archiveNode = $requestNode->addChild('archive');
		$archiveNode->addAttribute('filename', strval($this->id).'.zip');
		
		return $requestNode->asXML();
	}
	
	/**
	 * Update the problem's accept-to-submission ratio
	 *
	 * @param int $submission How much to add to submission
	 * @param int $accepted How much to add to accepted
	 */
	public function updateSubmissionStats($submission, $accepted)
	{
		Database::Get()->query("UPDATE `oj_problems` SET `accepted` = `accepted` + ({$accepted}), `submission` = `submission` + ({$submission})  WHERE `id` = {$this->id}");
	}
	
	public function add()
	{
		parent::add();
		foreach ($this->testCases as $c)
		{
			$c->problem = $this;
			$c->submit();
		}
	}
	
	public function checkPermission(User $user)
	{
		return $user->ableTo('view_problem') && (($this->listing > 0 && $this->dispatched > 0) || $user->id == $this->user->id || $user->ableTo('edit_problem') || $user->ableTo('view_problem_'.$this->id));
	}
}
?>