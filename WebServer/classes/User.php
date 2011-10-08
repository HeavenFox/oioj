<?php
import('ActiveRecord');

class User extends ActiveRecord
{
	private static $currentUser = null;
	
	private $acl = null;
	
	const ACL_OMNIPOTENT = 'omnipotent';
	
	public static function GetCurrentUser()
	{
		if (self::$currentUser)
		{
			return self::$currentUser;
		}
		
		if (IO::Session('user'))
		{
			return self::$currentUser = unserialize(IO::Session('user'));
		}
		
		if (IO::Cookie('uid'))
		{
			// Log in automatically
			$obj = self::fetch(array('id','username','password'),null,IO::Cookie('uid',0,'intval'));
			return self::$currentUser = $obj;
		}
		
		return null;
	}
	
	public function create()
	{
		
	}
	
	public function getACL()
	{
		$str = 'SELECT  `key` , SUM( `permission`) FROM (
SELECT  `key` ,  `permission` 
FROM  `oj_users_acl` WHERE `uid` = ?
UNION ALL 
SELECT  `key` ,  `permission` 
FROM  `oj_tags_acl` LEFT JOIN `oj_users_tags` USING (tid) WHERE `oj_users_tags`.`uid` = ?
) AS `perm_temp`
GROUP BY  `key`';
		$db = Database::Get();
		$stmt = $db->prepare($str);
		$stmt->execute($this->id,$this->id);
		foreach ($stmt as $v)
		{
			$this->acl[$v[0]] = $v[1];
		}
	}
	
	public function ableTo($key)
	{
		if (isset($this->acl[self::ACL_OMNIPOTENT]) && $this->acl[self::ACL_OMNIPOTENT] > 0)
		{
			return true;
		}
		return isset($this->acl[$key]) && $this->acl[$key] > 0;
	}
}
?>