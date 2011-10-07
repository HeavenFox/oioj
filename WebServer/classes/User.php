<?php
class User extends ActiveRecord
{
	
	private static $currentUser = null;
	
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
		}
		
		
		
		return null;
	}
	
	private static function ReadSession()
	{
		
	}
	
	public function getACL()
	{
		$str = 'SELECT  `key` , SUM( `permission`) 
FROM (

SELECT  `key` ,  `permission` 
FROM  `oj_users_acl` 
UNION ALL 
SELECT  `key` ,  `permission` 
FROM  `oj_tags_acl`
)
GROUP BY  `key`';
	}
	
	public function checkPermission($key)
	{
		if (isset($this->acl[self::ACL_OMNIPOTENT]) && $this->acl[self::ACL_OMNIPOTENT] > 0)
		{
			return true;
		}
		return isset($this->acl[$key]) && $this->acl[$key] > 0;
	}
}
?>