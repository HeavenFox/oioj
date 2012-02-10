<?php
abstract class Filter
{
	private $children;
	
	public function __construct($filter = null)
	{
		if ($filter)
		{
			$this->add($filter);
		}
	}
	
	public function add($filter)
	{
		if (is_array($filter))
		{
			$this->children = array_merge($this->children,$filter);
		}else
		{
			$this->children[] = $filter;
		}
	}
	
	protected function doValidate($data)
	{
		return true;
	}
	
	protected function doSanitize($data)
	{
		return $data;
	}
	
	public final function validate($data)
	{
		if (!$this->doValidate($data))
		{
			return false;
		}
		if ($this->children)
		{
			foreach ($this->children as $v)
			{
				if (!$v->validate($data))
				{
					return false;
				}
			}
		}
		return true;
	}
	
	public final function sanitize($data)
	{
		$ret = $this->doSanitize($data);
		
		if ($this->children)
		{
			foreach ($this->children as $v)
			{
				$ret = $v->sanitize($ret);
			}
		}
		return $ret;
	}
}
?>