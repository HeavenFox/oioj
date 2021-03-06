<?php
import('database.Database');
class ActiveRecord
{
	protected $_propValues = array();
	
	protected $_propUpdated = array();
	
	public static $schema;
	public static $tableName;
	public static $keyProperty = 'id';
	
	public static $count;
	
	public function __construct($id = null)
	{
		if ($id) {
			$this->_propValues[static::$keyProperty] = $id;
		}
	}
	
	/**
	 * Submit a record. Depending whether or not its primary key has a value, it will be updated or added
	 */
	public function submit()
	{
		if (isset($this->_propValues[static::$keyProperty])) {
			$this->update();
		} else {
			$this->add();
		}
	}
	
	/**
	 * Get composites of current record
	 * @param array $composites list of composite properties
	 */
	public function getComposite($composites)
	{
		foreach ($composites as $k => $v)
		{
			$this->findComposite($k,$v,'');
		}
	}
	
	public function findComposite($comp, $prop, $suffix)
	{
		$suffix = trim($suffix);
		if (strtoupper(substr($suffix,0,5)) == 'WHERE')
		{
			$suffix = 'AND '.substr($suffix,6);
		}
		$className = static::$schema[$comp]['class'];
		switch (static::$schema[$comp]['comp']) {
		case 'many':
			if (isset(static::$schema[$comp]['junction']))
			{
				$this->_propValues[$comp] = $className::find($prop,
														  'LEFT JOIN `'.static::$schema[$comp]['junction'].
														  '` ON (`'.$className::$tableName.'`.`'.$className::$keyProperty.'` = `'.static::$schema[$comp]['junction'].'`.`'.static::$schema[$comp]['column'][1].'`)'.
														  ' WHERE `'.static::$schema[$comp]['junction'].'`.`'. static::$schema[$comp]['column'][0] .'` = '.$this->_propValues[static::$keyProperty].' '.$suffix);
			
			}
			else
			{
				$this->_propValues[$comp] = $className::find($prop,'WHERE `'. static::$schema[$comp]['column'] .'` = '.$this->_propValues[static::$keyProperty].' '.$suffix);
			}
			break;
		case 'one':
			$this->_propValues[$comp] = $className::first($prop,'WHERE `'. static::$schema[$comp]['column'] .'` = '.$this->_propValues[static::$keyProperty].' '.$suffix);
			break;
		}
	}
	
	/**
	 * Check whether a property exists
	 * It may be easier to use isset instead
	 * @param string $prop property
	 * @return bool
	 */
	public function propertyExists($prop)
	{
		return isset($this->_propValues[$prop]);
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
			$queryStr .= self::Column($k);
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
				
				$queryStr .= self::Column($k);
				$queryStr .= ' = ?';
				$arr[] = $this->getDatabaseRepresentation($k);
			}
			$queryStr .= " WHERE `".static::$keyProperty."` = ".$this->_propValues[static::$keyProperty];
		}
		$stmt = Database::Get()->prepare($queryStr);
		
		$stmt->execute($arr);
	}
	
	/**
	 * Find records matching specific criteria
	 * 
	 * @param array $properties
	 * @param string $suffix
	 * @param array $data
	 */
	public static function find($properties, $suffix = '', $data = array())
	{
		$queryStr = self::_makeQueryString($properties, $suffix);
		$resultSet = array();
		$stmt = Database::Get()->prepare($queryStr);
		$stmt->execute($data);
		
		foreach($stmt as $row)
		{
			$obj = new static;
			$obj->_fillRow($row, $properties);
			$resultSet[] = $obj;
		}
		return $resultSet;
	}
	
	public static function first($properties, $suffix = '', $data = array())
	{
		$obj = new static;
		return $obj->fetch($properties, $suffix, $data);
	}
	
	/**
	 * Fetch properties for existing or a newly-initialized record object
	 *
	 * @param array $properties properties
	 * @param mixed $suffix ID or suffix clause. Leave blank to fetch into current
	 * @param array $data data for prepared query
	 */
	public function fetch($properties, $suffix = null, $data = array())
	{
		if ($suffix === null)
		{
			$suffix = $this->_makeIdClause($this->_propValues[static::$keyProperty]);
		}
		else if (is_int($suffix))
		{
			$this->_propValues[static::$keyProperty] = $suffix;
			$suffix = $this->_makeIdClause($suffix);
		}
		
		$queryStr = self::_makeQueryString($properties, $suffix . ' LIMIT 0,1');
		
		$stmt = Database::Get()->prepare($queryStr);
		$stmt->execute($data);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		
		if ($row) {
			$this->_fillRow($row, $properties);
			return $this;
		} else {
			return null;
		}
	}
	
	public static function firstByQuery($properties, $query, $data)
	{
		
	}
	
	public static function findByQuery($properties, $query, $data)
	{
		
	}
	
	public function fetchByQuery($properties, $query, $data)
	{
		
	}
	
	public static function Table()
	{
		return '`'.static::$tableName.'`';
	}
	
	public static function Column($k)
	{
		if (isset(static::$schema[$k]['column']))
		{
			$k = static::$schema[$k]['column'];
		}
		return '`'.static::$tableName.'`.`'.$k.'`';
	}
	
	/**
	 * Validate a record or property
	 */
	public function validate($prop = null)
	{
		if ($prop)
		{
			if (($call = static::$schema[$k]['validator']))
			{
				
					$call($this->_propValues[$k]);
				
			}
			return true;
		}
		else
		{
			$wrong = array();
			foreach ($this->_propUpdated as $k => $v)
			{
				try
				{
					$this->validate($k);
				}
				catch(InputException $e)
				{
					$wrong[$k] = $e;
				}
			}
			if (count($wrong) == 0)
			{
				return false;
			}
			return $wrong;
		}
	}
	
	/**
	 * Sanitize a record
	 */
	public function sanitize()
	{
		foreach ($this->_propUpdated as $k => $v)
		{
			if ($call = static::$schema[$k]['sanitizer'])
			{
				$this->_propValues[$k] = $call($this->_propValues[$k]);
			}
		}
	}
	
	public function _setProp($param, $value)
	{
		if (isset(static::$schema[$param]['comp']) && !($value instanceof ActiveRecord))
		{
			$className = static::$schema[$param];
			$value = new $className($value);
		} else if (!isset(static::$schema[$param]['unserializer']))
		{
			$value = $this->toPropertyType($param, $value);
		}
		
		$this->_propValues[$param] = $value;
	}
	
	public function _fillRow($row, $properties)
	{
		$i = 0;
		if (is_array($properties))
		{
			foreach ($properties as $prop => $v)
			{
				if (is_int($prop))
				{
					$this->_preprocessSet($v,$row[$i]);
					$i++;
				}else
				{
/*
			}
		}
		if (is_array($composites))
		{
			foreach ($composites as $prop => $comp)
			{
*/
					$comp = is_array($v) ? $v : array($v);
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
	}
	
	private function _makeIdClause($id)
	{
		return 'WHERE '.static::Column(static::$keyProperty).' = '.$id;
	}
	
	public function _preprocessGet($param)
	{
		if (isset(static::$schema[$param]['serializer']))
		{
			$call = static::$schema[$param]['serializer'];
			return $call($this->_propValues[$param]);
		}
		return $this->_propValues[$param];
	}
	
	protected function toPropertyType($prop, $value)
	{
		$processFunc = array(
			'int' => 'intval',
			'double' => 'floatval',
			'string' => 'strval',
			'text' => 'strval',
			'bool' => function($data){
				return $data ? 1 : 0;
			}
		);
		
		if (isset($processFunc[static::$schema[$prop]['class']]))
		{
			$func = $processFunc[static::$schema[$prop]['class']];
			$value = $func($value);
		}
		return $value;
	}
	
	public function _preprocessSet($param, $value)
	{
		$value = $this->toPropertyType($param, $value);
		
		if (isset(static::$schema[$param]['unserializer']))
		{
			$call = static::$schema[$param]['unserializer'];
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
		unset($this->_propValues[static::$keyProperty]);
	}
	
	protected static function _makeColumnList($properties)
	{
		$queryStr = '';
		$first = true;
		if ($properties)
		{
			foreach ($properties as $k => $v)
			{
				if (is_int($k))
				{
					// It is a normal property
					$prop = $v;
					
					if ($first){
						$first = false;
					}else
					{
						$queryStr .= ',';
					}
					if (isset(static::$schema[$prop]['query']))
					{
						$queryStr .= '('.static::$schema[$prop]['query'].')';
					}
					else
					{
						$queryStr .= self::Column($prop);
					}
					
				}
				else
				{
					// It is a composite
					$v = is_array($v) ? $v : array($v);
					
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
							$queryStr.= $className::Column($prop);
						}
					}
				}
			}
		}
		return $queryStr;
	}
	
	protected static function _makeQueryString($properties, $suffix)
	{
		$queryStr = 'SELECT ';
		
		$queryStr .= self::_makeColumnList($properties);
		
		$queryStr .= " FROM `".static::$tableName."` ";
		
		if ($properties)
		{
			foreach ($properties as $k => $v)
			{
				if (is_string($k))
				{
					if (static::$schema[$k]['comp'] == 'one')
					{
						$className = static::$schema[$k]['class'];
					
						$queryStr.='LEFT JOIN `'.$className::$tableName.'` ON `'.$className::$tableName.'`.`'.$className::$keyProperty.'`='.static::Column($k).' ';
						
					}
				}
			}
		}
		$queryStr .= $suffix;
		return $queryStr;
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
	
	public function __get($param)
	{
		return $this->_propValues[$param];
	}
	
	public function __set($param, $value)
	{
		$this->_propUpdated[$param] = true;
		$this->_setProp($param, $value);
	}
	
	public function __isset($param)
	{
		return isset($this->_propValues[$param]);
	}
	
	public function __sleep()
	{
		return array('_propValues');
	}
}
?>