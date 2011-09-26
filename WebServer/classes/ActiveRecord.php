<?php
class ActiveRecord
{
	protected $_tableName = '';
	
	protected $_properties = array();
	
	protected $_propertiesClass = array();
	
	protected $_composites = array();
	
	protected $_cross = array();
	
	protected $propValues = array();
	
	protected $_propUpdated = array();
	
	protected $_rowIDProperty = '';
	protected $_rowIDColumn = '';
	
	protected $_columnProperty = array();
	
	public function createTable()
	{
		
	}
	
	public function submit()
	{
		
	}
	
	protected function _getComposites()
	{
		
	}
	
	public function add()
	{
		
	}
	
	public function update()
	{
		if (count($this->_propUpdated) > 0)
		{
			$db = Database::Get();
			$queryStr = "UPDATE {$this->_tableName} SET ";
			foreach ($this->_propUpdated as $k => $v)
			{
				$this->$k
			}
			$queryStr .= " WHERE `{$this->_rowIDColumn}` = {}";
		}
	}
	
	public function fetch($id)
	{
		
	}
	
	public function getRowID()
	{
		$prop = $this->_rowIDProperty;
		return $this->$prop;
	}
	
	public function fillRow($row)
	{
		
	}
	
	protected function columnToProperty($column)
	{
		
	}
	
	public static function find()
	{
		
	}
	
	public function remove()
	{
		
	}
	
	public function __get($param)
	{
		return $this->propValues[$param];
	}
	
	public function __set($param, $value)
	{
		$this->_propUpdated[$param] = true;
		$this->propValues[$param] = $value;
	}
}
?>