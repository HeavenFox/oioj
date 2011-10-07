<?php
import('database.Database');
class ActiveRecord
{
	/*
	protected $_tableName = '';
	
	protected $_schema = array();
	*/
	protected $_propValues = array();
	
	protected $_propUpdated = array();
	
	/*
	protected $_rowIDProperty = '';
	
	protected static $_db;
	*/
	
	public function __construct()
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
	
	public function propertyExists($prop)
	{
		return isset($this->_propValues[$prop]);
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
			$queryStr .= "`".static::$tableName."`.`{$prop}`";
		}
		
		$queryStr .= " FROM `".static::$tableName."` ";
		$queryStr .= $suffix;
		
		return $queryStr;
	}
	
	/**
	 * Find records matching specific criteria
	 * 
	 * @param array $properties
	 * @param array $composites
	 * @param string $suffix
	 * @todo finish composite function
	 */
	public static function find($properties, $composites, $suffix)
	{
		$queryStr = self::_makeQueryString($properties, $composites, $suffix);
		$resultSet = array();
		$stmt = Database::Get()->query($queryStr);
		foreach($stmt as $row)
		{
			$obj = new static;
			/*
			for ($i = 0; $i < count($properties); $i++)
			{
				$obj->_propValues[$properties[$i]] = $row[$i];
			}*/
			$obj->_fillRow($row,$properties, $composites);
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
	
	public static function findByQuery($query, $properties, $composites)
	{
		
	}
	
	public function fetchByQuery($query, $properties, $composites)
	{
		
	}
	
	public function _setProp($prop, $val)
	{
		$this->_propValues[$prop] = $val;
	}
	
	
	public function _fillRow($row, $properties, $composites)
	{
		$i = 0;
		foreach ($properties as $v)
		{
			$this->_propValues[$v] = $row[$i];
			$i++;
		}
		foreach ($composites as $prop => $comp)
		{
			foreach ($comp as $v)
			{
				$this->_propValues[$prop]->_setProp($v,$row[$i]);
				$i++;
			}
		}
	}
	
	/**
	 * Remove current record
	 * Enter description here ...
	 * @param array $composites list of composites that need to be deleted
	 */
	public function remove($composites = null)
	{
		$kp = static::$keyProperty;
		$this->_db->query('DELETE FROM `'.self::$tableName.'` WHERE `'.$kp.'` = '.$this->$kp);
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
	
	public function __sleep()
	{
		return array('_propValues');
	}
}
?>