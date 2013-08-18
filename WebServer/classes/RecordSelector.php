<?php
class RecordSelector
{
	private $className;
	
	public function __construct($cls)
	{
		$this->className = $cls;
	}
	
	/**
	 * Find records at a specific page
	 *
	 * @param int $page Requested page number
	 * @param int $perPage Page number per page
	 * @param int $maxPage Variable to store number of maximum page
	 * @param int $properties Properties of ActiveRecord to fetch
	 * @param string $suffix Suffix of selection. No LIMIT clause should be included
	 * @param array $data Data to fill in prepared statement
	 * @param callback $call Callback function to execute record fetching. Default to class' find method. It takes four arguments: properties, suffix, limit clause and data. This should be used for, e.g. TaggedRecord
	 */
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