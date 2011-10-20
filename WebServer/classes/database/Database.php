<?php
/**
 * Database Manager
 * HeavenFox Base Library
 */
class Database
{
    private static $connection = null;
    
    /**
     * Get a database connection object
     *
     * @return object database connection
     */
    public static function Get($params = NULL)
    {
        if (self::$connection == null && $params != NULL)
        {
            self::$connection = self::CreateConnection($params);
        }
        return self::$connection;
    }
    
    private static function CreateConnection($params)
    {
    	$obj = null;
        switch($params['driver'])
        {
		case 'pdo_mysql':
			$obj = new PDO('mysql:host='.$params['host'].';dbname='.$params['database'],$params['username'],$params['password']);
			$obj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			break;
		case 'mysqli':
			require ROOT. 'classes/database/drivers/MySQLiObject.php';
			$obj = new MySQLiObject('mysql:host='.$params['host'].';dbname='.$params['database'],$params['username'],$params['password']);
			break;
        default:
            throw new Exception('Invalid Database Driver!');
        }
        $obj->exec("SET NAMES 'utf8'");
        return $obj;
    }
}
?>
