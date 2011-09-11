<?php
/**
 * PDO Adapter for PHP MySQLi Extension
 */
class MySQLiObject
{
	private $conn;
	
	public function __construct($dsn, $username, $password)
	{
		$matches = array();
		preg_match('/dbname=([^;]+)/', $dsn, $matches);
		$dbname = $matches[1];
		preg_match('/host=([^;]+)/', $dsn, $matches);
		$host = $matches[1];
		
		$this->conn = new mysqli($host, $username, $password, $dbname);
	}
	
	public static function getAvailableDrivers()
	{
		return array('mysql');
	}
	
	public function beginTransaction()
	{
		return $this->conn->autocommit(false);
	}
	
	public function commit()
	{
		return $this->conn->commit();
	}
	
	public function errorCode()
	{
		return $this->conn->errno;
	}
	
	public function errorInfo()
	{
		return $this->conn->error;
	}
	
	public function exec($q)
	{
		$this->conn->query($q);
		return $r->affected_rows;
	}
	
	public function lastInsertId()
	{
		return $this->conn->insert_id;
	}
	
	public function prepare($q)
	{
		return new MySQLiStatement($this->conn->prepare($q));
	}
	
	public function query($q)
	{
		$r = $this->conn->query($q);
		if (is_bool($r))return $r;
		return new MySQLiStatement($r);
	}
	
	public function quote($str)
	{
		return $this->conn->real_escape_string($str);
	}
	
	public function rollBack()
	{
		return $this->conn->rollback();
	}
}

class MySQLiStatement implements Iterator
{
	private $stmt = null;
	private $result = null;
	private $defaultFetchMode = 4;
	private $curValue = null;
	
	private $map = array(2 => MYSQLI_ASSOC, 3 => MYSQLI_NUM, 4 => MYSQLI_BOTH);
	
	public function __construct($param)
	{
		if ($param instanceof MySQLi_STMT)
		{
			$this->stmt = $param;
		}else if ($param instanceof MySQLi_Result)
		{
			$this->result = $param;
		}else
		{
			throw new Exception('Invalid argument');
		}
	}
	
	public function __destruct()
	{
		if ($this->result)
			$this->result->close();
		if ($this->stmt)
			$this->stmt->close();
	}
	
	public function columnCount()
	{
		return $this->result->field_count;
	}
	
	/*
	 * Note: due to diffenences in implementation, bindColumn and bindParam are not implemented yet
	 */
	
	public function errorCode()
	{
		return $this->stmt->errno;
	}
	
	public function errorInfo()
	{
		return $this->stmt->error;
	}
	
	public function execute($params = null)
	{
		if ($this->stmt !== null)
		{
			if (is_array($params))
			{
				// Require reference
				$types = str_pad('',count($params),'s');
				$args = array(&$types);
				$paramsRef = array();
				foreach ($params as $k => $v)
				{
					$args[] = &$params[$k];
				}
				
				call_user_func_array(array($this->stmt,'bind_param'), $args);
			}
			$this->stmt->execute();
			$this->result = $this->stmt->result_metadata();
		}
	}
	
	public function fetch($fetch_style = null)
	{
		if (!$this->result)
		{
			return null;
		}else
		{
			if ($fetch_style === null)$fetch_style = $this->defaultFetchMode;
			switch ($fetch_style)
			{
			case 8: // PDO::FETCH_CLASS
			case 5: // PDO::FETCH_OBJ
				return $this->result->fetch_object();
			default:
				return $this->result->fetch_array($this->map[$fetch_style]);
			}
		}
	}
	
	public function fetchAll($fetch_style = null)
	{
		if ($fetch_style === null)$fetch_style = $this->defaultFetchMode;
		
		return $this->result->fetch_all($this->map[$fetch_style]);
	}
	
	public function fetchColumn($c = 0)
	{
		$arr = $this->result->fetch_row();
		return $arr[$c];
	}
	
	public function rowCount()
	{
		return $this->result->num_rows;
	}
	
	public function setFetchMode($mode)
	{
		$this->defaultFetchMode = $mode;
	}
	
	/*
	 * Iterator Operations
	 */
	public function rewind()
	{
		if (!$this->result)
		{
			if ($this->stmt)
			{
				$this->execute();
			}else
			{
				return;
			}
		}
		$this->next();
	}
	
	public function current()
	{
		return $this->curValue;
	}
	
	public function key()
	{
	}
	
	public function next()
	{
		$this->curValue = $this->fetch();
	}
	
	public function valid()
	{
		return $this->curValue !== null;
	}
}
?>