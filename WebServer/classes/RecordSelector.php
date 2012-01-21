<?php
class RecordSelector
{
	private $className;
	
	public function __construct($cls)
	{
		$this->className = $cls;
	}
	
	public function findAtPage($page, $perPage, &$maxPage, $properties, $suffix = '', $data = NULL)
	{
		$cls = $this->className;
		
		$rec = $cls::first(array('count'), $suffix, $data);
		$num = $rec->count;
		
		$maxPage = ceil($num/$perPage);
		$limit = ' LIMIT '.$perPage * ($page-1).','.$perPage;
		
		$suffix .= $limit;
		
		return $cls::find($properties, $suffix, $data);
	}
	
	public function adjacentPages($page, $maxPage, $width)
	{
		
	}
	
	public function randomRecord()
	{
		
	}
}
?>