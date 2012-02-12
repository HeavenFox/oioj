<?php
import('ActiveRecord');
class Attachment extends ActiveRecord
{
	static $schema = array(
		'id' => array('class' => 'int'),
		'filename' => array('class' => 'string'),
		'storedname' => array('class' => 'string')
	);
	
	static $rootDir;
	
	public function getStoredLocation()
	{
		return static::$rootDir . $this->storedname;
	}
	
	public function download($resumable = true)
	{
		$fp = fopen($this->getStoredLocation(),'rb');
		$fsize = filesize($fname);
		
		if ($resumable && isset($_SERVER['HTTP_RANGE']) && ($_SERVER['HTTP_RANGE'] != "") && preg_match("/^bytes=([0-9]+)-/i", $_SERVER['HTTP_RANGE'], $match) && ($match[1] < $fsize))
		{ 
			$start = intval($match[1]);
		}
		else
		{
			$start = 0;
		}
		
		if ($start > 0)
		{
			fseek($fp, $start);
			header('HTTP/1.1 206 Partial Content');
			header('Content-Length: ' . ($fsize - $start));
			header('Content-Ranges: bytes ' . $start . '-' . ($fsize - 1) . '/' . $fsize);
		}
		else
		{
			header("Content-Length: {$fsize}");
			Header("Accept-Ranges: bytes");
		}
		
		header("Content-Type: application/octet-stream"); 
		header("Content-Disposition: attachment;filename={$this->filename}");
		
		fpassthru($fp);
	}
}
?>