<?php
class RecordSelector
{
	private $className;
	
	public function __construct($cls)
	{
		$this->className = $cls;
	}
	
	public function findAtPage($page, $perPage, &$maxPage, $properties, $suffix = '', $data = NULL, $call = NULL)
	{
		$cls = $this->className;
		
		if ($call)
		{
			$recs = $call(array('count'), $suffix, ' LIMIT 0,1', $data);
			$rec = $recs[0];
		}else
		{
			$rec = $cls::first(array('count'), $suffix, $data);
		}
		$num = $rec->count;
		
		$maxPage = ceil($num/$perPage);
		$limit = ' LIMIT '.$perPage * ($page-1).','.$perPage;
		
		if ($call)
		{
			return $call($properties, $suffix, $limit, $data);
		}
		else
		{
			$suffix .= $limit;
			
			return $cls::find($properties, $suffix, $data);
		}
	}
	
	public function randomRecord($properties, $suffix, $call)
	{
		
	}
}
?>