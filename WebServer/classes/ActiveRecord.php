<?php
import('database.Database');
class ActiveRecord
{
	protected $_propValues = array();
	
	protected $_propUpdated = array();
	
	public static $schema;
	public static $tableName;
	public static $keyProperty = 'id';
	
	public function __construct($id = null)
	{
		if ($id) {
			$this->_propValues[static::$keyProperty] = $id;
		}
	}
	
	
	public function submit()
	{
		if (isset($this->_propValues[static::$keyProperty])) {
			$this->update();
		} else {
			$this->add();
		}
	}
	
	public function getComposite($composites)
	{
		foreach ($composites as $k => $v)
		{
			$className = static::$schema[$k]['class'];
			switch (static::$schema[$k]['comp']) {
			case 'many':
				$this->_propValues[$k] = $className::find($v,null,'WHERE `'. static::$schema[$k]['column'] .'` = '.$this->_propValues[static::$keyProperty]);
				break;
			case 'one':
				$this->_propValues[$k] = $className::first($v,null,'WHERE `'. static::$schema[$k]['column'] .'` = '.$this->_propValues[static::$keyProperty]);
				break;
			case 'junction':
				
				break;
			}
		}
	}
	
	public function add()
	{
		$queryStr = 'INSERT INTO `';
		$queryStr .= static::$tableName;
		$queryStr .= '` (';
		$first = true;
		foreach ($this->_propUpdated as $k => $v)
		{
			if ($first) {$first = false;} else {$queryStr .= ',';}
			$queryStr .= "`".self::_getDatabaseColumn($k)."`";
		}
		$queryStr .= ') VALUES (';
		$arr = array();
		$first = true;
		foreach ($this->_propUpdated as $k => $v)
		{
			if ($first) {
				$first = false;
			} else {
				$queryStr .= ',';
			}
			$queryStr .= '?';
			$arr[] = $this->getDatabaseRepresentation($k);
		}
		$queryStr .= ')';
		$stmt = Database::Get()->prepare($queryStr);
		
		$stmt->execute($arr);
		
		$this->_propValues[static::$keyProperty] = Database::Get()->lastInsertId();
	}
	
	public function propertyExists($prop)
	{
		return isset($this->_propValues[$prop]);
	}
	
	public static function _getDatabaseColumn($k)
	{
		if (isset(static::$schema[$k]['column']))
		{
			return static::$schema[$k]['column'];
		}
		return $k;
	}
	
	private function getDatabaseRepresentation($k)
	{
		if (isset(static::$schema[$k]['comp']))
		{
			$className = static::$schema[$k]['class'];
			$idprop = $className::$keyProperty;
			return $this->_propValues[$k]->$idprop;
		}
		else
		{
			return $this->_preprocessGet($k);
		}
	}
	
	public function update()
	{
		if (count($this->_propUpdated) > 0)
		{
			$queryStr = "UPDATE `".static::$tableName."` SET ";
			$arr = array();
			$first = true;
			foreach ($this->_propUpdated as $k => $v)
			{
				if ($first)
					$first = false;
				else
					$queryStr .= ',';
				
				$queryStr .= self::_getDatabaseColumn($k);
				$queryStr .= ' = ?';
				$arr[] = $this->getDatabaseRepresentation($k);
			}
			$queryStr .= " WHERE `".static::$keyProperty."` = ".$this->_propValues[static::$keyProperty];
		}
		$stmt = Database::Get()->prepare($queryStr);
		
		$stmt->execute($arr);
	}
	
	protected static function _makeQueryString($properties, $composites, $suffix)
	{
		$queryStr = 'SELECT ';
		$first = true;
		if ($properties)
		{
			foreach ($properties as $prop)
			{
				if ($first){
					$first = false;
				}else
				{
					$queryStr .= ',';
				}
				$queryStr .= "`".static::$tableName."`.`".self::_getDatabaseColumn($prop)."`";
			}
		}
		
		if ($composites)
		{
			foreach ($composites as $k => $v)
			{
				if (static::$schema[$k]['comp'] == 'one')
				{
					$className = static::$schema[$k]['class'];
					
					foreach ($v as $prop)
					{
						if ($first){
							$first = false;
						}else
						{
							$queryStr .= ',';
						}
						$queryStr.='`'.$className::$tableName.'`.`'.$className::_getDatabaseColumn($prop).'`';
					}
				}
			}
		}
		
		$queryStr .= " FROM `".static::$tableName."` ";
		
		if ($composites)
		{
			foreach ($composites as $k => $v)
			{
				if (static::$schema[$k]['comp'] == 'one')
				{
					$className = static::$schema[$k]['class'];
				
					$queryStr.='LEFT JOIN `'.$className::$tableName.'` ON `'.$className::$tableName.'`.`'.$className::$keyProperty.'`=`'.static::$tableName.'`.`'.static::$schema[$k]['column'].'` ';
					
				}
			}
		}
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
	public static function find($properties, $composites, $suffix = '', $data = array())
	{
		$queryStr = self::_makeQueryString($properties, $composites, $suffix);
		$resultSet = array();
		$stmt = Database::Get()->prepare($queryStr);
		$stmt->execute($data);
		
		foreach($stmt as $row)
		{
			$obj = new static;
			/*
			for ($i = 0; $i < count($properties); $i++)
			{
				$obj->_propValues[$properties[$i]] = $row[$i];
			}*/
			$obj->_fillRow($row, $properties, $composites);
			$resultSet[] = $obj;
		}
		return $resultSet;
	}
	
	public static function first($properties, $composites, $suffix = '', $data = array())
	{
		$obj = new static;
		return $obj->fetch($properties, $composites, $suffix, $data);
	}
	
	public function fetch($properties, $composites, $suffix, $data = array())
	{
		if (is_int($suffix))
		{
			$suffix = 'WHERE `'.static::$tableName.'`.`'.static::$keyProperty.'` = '.$suffix;
		}
		$queryStr = self::_makeQueryString($properties, $composites, $suffix);
		
		$stmt = Database::Get()->prepare($queryStr);
		$stmt->execute($data);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		
		if ($row) {
			$this->_fillRow($row, $properties, $composites);
			return $this;
		} else {
			return null;
		}
		
		//return static::first($query, $properties, 'WHERE `'.static::$keyProperty . "` = '{$id}'");
	}
	
	public static function findByQuery($query, $properties, $composites)
	{
	}
	
	public function fetchByQuery($query, $properties, $composites)
	{
		
	}
	
	public function _setProp($param, $value)
	{
		if (isset(static::$schema[$param]['comp']) && !($value instanceof ActiveRecord))
		{
			$className = static::$schema[$param];
			$value = new $className($value);
		}
		
		
		$this->_propValues[$param] = $value;
		
	}
	
	
	public function _fillRow($row, $properties, $composites)
	{
		$i = 0;
		if (is_array($properties))
		{
			foreach ($properties as $v)
			{
				$this->_preprocessSet($v,$row[$i]);
				$i++;
			}
		}
		if (is_array($composites))
		{
			foreach ($composites as $prop => $comp)
			{
				if (!isset($this->_propValues[$prop]) || !($this->_propValues[$prop] instanceof ActiveRecord))
				{
					$className = static::$schema[$prop]['class'];
					$this->_propValues[$prop] = new $className;
				}
				foreach ($comp as $v)
				{
					$this->_propValues[$prop]->_preprocessSet($v,$row[$i]);
					$i++;
				}
			}
		}
	}
	
	public function _preprocessGet($param)
	{
		if (isset(static::$schema[$param]['setter']))
		{
			$call = static::$schema[$param]['setter'];
			return $call($this->_propValues[$param]);
		}
		return $this->_propValues[$param];
	}
	
	public function _preprocessSet($param, $value)
	{
		$processFunc = array(
			'int' => 'intval',
			'double' => 'floatval',
		);
		if (isset($processFunc[static::$schema[$param]['class']]))
		{
			$func = $processFunc[static::$schema[$param]['class']];
			$value = $func($value);
		}
		if (isset(static::$schema[$param]['getter']))
		{
			$call = static::$schema[$param]['getter'];
			$this->_propValues[$param] = $call($value);
		}
		else
		{
			$this->_propValues[$param] = $value;
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
		/*if (is_array($composites))
		{
			foreach($composites as $composite)
			{
				
			}
		}*/
	}
	
	public function __get($param)
	{
		return $this->_propValues[$param];
	}
	
	public function __set($param, $value)
	{
		$this->_propUpdated[$param] = true;
		$this->_setProp($param, $value);
	}
	
	public function __sleep()
	{
		return array('_propValues');
	}
}
?>