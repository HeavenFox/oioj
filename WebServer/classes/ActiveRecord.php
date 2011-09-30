<?php
class ActiveRecord
{
	protected $_tableName = '';
	
	protected $_properties = array();
	
	protected $_propertiesClass = array();
	
	protected $_hasMany = array();
	protected $_hasOne = array();
	
	protected $_cross = array();
	
	protected $_propValues = array();
	
	protected $_propUpdated = array();
	
	protected $_rowIDProperty = '';
	
	protected $_columnProperty = array();
	
	protected $_db;
	
	public function __construct()
	{
		$this->_db = Database::Get();
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
			$queryStr = "UPDATE {$this->_tableName} SET ";
			foreach ($this->_propUpdated as $k => $v)
			{
				$this->$k
			}
			$queryStr .= " WHERE `{$this->_rowIDColumn}` = {}";
		}
	}
	
	public function fetch($condition)
	{
		
	}
	
	public function fetchByQuery($query, $parameters, $properties, $composites)
	{
		
	}
	
	public function getRowID()
	{
		$prop = $this->_rowIDProperty;
		return $this->$prop;
	}
	
	public function _fillRow($row)
	{
		
	}
	
	
	protected function _columnToProperty($column)
	{
		
	}
	
	public static function find($properties, $composites, $suffix)
	{
		
	}
	
	public function remove($composites = null)
	{
		$this->_db->query("");
		if (is_array($composites))
		{
			foreach($composites as $composite)
			{
				$
			}
		}
	}
	
	public function __get($param)
	{
		return $this->_propValues[$param];
	}
	
	public function __set($param, $value)
	{
		$this->_propUpdated[$param] = true;
		$this->_propValues[$param] = $value;
	}
}
?>