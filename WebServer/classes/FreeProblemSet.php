<?php
import('Problem');
import('Unit');
import('JudgeServer');

class FreeProblemSet
{
	private $filename;
	public $problems;
	
	public $defaultIO = self::IO_SCREEN;
	
	public $defaultCompare = Problem::COMPARE_OMITSPACE;
	
	const IO_SCREEN = 1;
	const IO_FILE = 2;
	
	const IO_DEFAULT_IN = 'data.in';
	const IO_DEFAULT_OUT = 'data.out';
	
	public $defaultScore = 10;
	
	public function __construct($file)
	{
		$this->filename = $file;
	}
	
	public function parse($handler = NULL)
	{
		$reader = new XMLReader();
		$reader->open($this->filename);
		
		/*
		$inputContents = array();
					$answerContents = array();
					
					$title = null;
					OIOJ::$template->clearAllAssign();
					$timelimit = null;
					$memorylimit = null;
					
					$source = null;
					$solution = null;
		*/
		
		while ($reader->read())
		{
			switch ($reader->nodeType)
			{
			case XMLREADER::ELEMENT:
				switch ($reader->name)
				{
				case 'item':
					$inputContents = array();
					$answerContents = array();
					
					$title = null;
					OIOJ::$template->clearAllAssign();
					$timelimit = null;
					$memorylimit = null;
					
					$source = null;
					$solution = null;
					
					break;
				case 'title':
					$reader->read();
					$title = $reader->value;
					break;
				case 'source':
					$reader->read();
					$source = $reader->value;
					break;
				case 'solution':
					$reader->read();
					$solution = $reader->value;
					break;
				case 'description':
				case 'input':
				case 'output':
				case 'sample_input':
				case 'sample_output':
				case 'hint':
					$name = $reader->name;
					$reader->read();
					OIOJ::$template->assign($name, nl2br($reader->value));
					break;
				case 'time_limit':
					if ($u = $reader->getAttribute('unit'))
					{
						$reader->read();
						$timelimit = Unit::Convert(floatval($reader->value),$u,'s');
					}
					else
					{
						$reader->read();
						OIOJ::$template->assign('time_limit', nl2br($reader->value));
					}
					break;
				case 'memory_limit':
					if ($u = $reader->getAttribute('unit'))
					{
						$reader->read();
						$memorylimit = intval(Unit::Convert(floatval($reader->value),$u,'mb'));
					}
					else
					{
						$reader->read();
						OIOJ::$template->assign('memory_limit', nl2br($reader->value));
					}
					break;
				case 'test_input':
					$reader->read();
					$inputContents[]=$reader->value;
					break;
				case 'test_output':
					$reader->read();
					$answerContents[]=$reader->value;
					break;
				}
				break;
			case XMLREADER::END_ELEMENT:
				if ($reader->name == 'item')
				{
					$cur = new Problem;
					
					$cur->title = $title;
					$cur->body = OIOJ::$template->fetch('freeproblemset.tpl');
					$cur->source = $source;
					$cur->solution = $solution;
					$cur->type = Problem::TYPE_CLASSIC;
					
					if ($this->defaultIO == self::IO_FILE)
					{
						$cur->input = self::IO_DEFAULT_IN;
						$cur->output = self::IO_DEFAULT_OUT;
					}
					else if ($this->defaultIO == self::IO_SCREEN)
					{
						$cur->input = $cur->output = Problem::IO_SCREEN;
					}
					
					for ($i = 0; $i < count($inputContents); $i++)
					{
						$cur->addCaseByContent($inputContents[$i],$answerContents[$i],$timelimit,$memorylimit,$this->defaultScore);
					}
					
					$cur->compare = $this->defaultCompare;
					$this->problems[] = $cur;
					if ($handler)
					{
						$handler($cur);
					}
				}
				break;
			}
		}
	}
	
	public function dispatch($server = null)
	{
		if (!$server)
		{
			$servers = JudgeServer::find(array('id','name','ip','port','ftpUsername','ftpPassword'));
			foreach ($servers as $s)
			{
				$this->dispatch($s);
			}
		}
		else
		{
			foreach ($this->problems as $p)
			{
				echo 'Dispatching ' . $p->title . '<br />';
				$p->submit();
				$p->createArchive();
				$p->dispatch($server);
			}
		}
	}
	
	public function queueForDispatch($handler = NULL)
	{
		foreach ($this->problems as $p)
		{
			$p->submit();
			$p->createArchive();
			$p->queueForDispatch();
			if ($handler)
			{
				$handler($p);
			}
		}
	}
}
?>