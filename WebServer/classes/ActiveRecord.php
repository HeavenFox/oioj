<?php
class ActiveRecord
{
	protected $_tableName = '';
	
	protected $_schema = array();
	
	protected $_propValues = array();
	
	protected $_propUpdated = array();
	
	protected $_rowIDProperty = '';
	
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
			}
			$queryStr .= " WHERE `{$this->_rowIDColumn}` = {}";
		}
	}
	
	protected static function _makeQueryString($properties, $composites, $suffix)
	{
		$queryStr = 'SELECT ';
		$first = true;
		foreach ($properties as $prop)
		{
			if ($first){
				$first = false;
			}else
			{
				$queryStr .= ',';
			}
			$queryStr .= "`{self::$tableName}`.`{$prop}`";
		}
		
		$queryStr .= " FROM `{self::$tableName}` ";
		$queryStr .= $suffix;
		
		return $queryStr;
	}
	
	/**
	 * Find records matching specific criteria
	 * @param array $properties
	 * @param array $composites
	 * @param string $suffix
	 * @todo finish composite function
	 */
	public static function find($properties, $composites, $suffix)
	{
		$queryStr = self::_makeQueryString($properties, $composites, $suffix);
		$resultSet = array();
		foreach(Database::Get()->query($queryStr) as $row)
		{
			$obj = new self;
			for ($i = 0; $i < count($properties); $i++)
			{
				$obj->_propValues[$properties[$i]] = $row[$i];
			}
			$resultSet[] = $obj;
		}
		return $resultSet;
	}
	
	public static function first()
	{
		
	}
	
	public function fetch($condition)
	{
		
	}
	
	public function findByQuery($query, $properties, $composites)
	{
		
	}
	
	
	public function _fillRow($row)
	{
		foreach ($row as $k => $v)
		{
			if (is_string($k))
			{
				$this->_propValues[$k] = $v;
			}
		}
	}
	
	
	
	public function remove($composites = null)
	{
		$this->_db->query("");
		if (is_array($composites))
		{
			foreach($composites as $composite)
			{
				
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