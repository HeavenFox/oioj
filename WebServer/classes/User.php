<?php
import('ActiveRecord');

class User extends ActiveRecord
{
	static $tableName = 'oj_users';
	
	private static $currentUser = null;
	
	protected $acl = null;
	
	const ACL_OMNIPOTENT = 'omnipotent';
	
	public static function GetCurrent()
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
			$obj = self::first(array('id','username','password'),null,'WHERE `id` = '.IO::Cookie('uid',0,'intval'));
			if ($obj && IO::Cookie('password') == $obj->password)
			{
				return self::$currentUser = $obj;
			}
		}
		
		return self::$currentUser = new GuestUser();
	}
	
	public static function DestroySession()
	{
		IO::DestroySession();
		IO::SetCookie('uid',0,-4200);
		self::$currentUser = new GuestUser();
	}
	
	public function createSession()
	{
		self::$currentUser = $this;
		IO::SetSession('user',serialize($this));
	}
	
	
	public function add()
	{
		$this->salt = md5(rand());
		$this->password = sha1($this->password.$this->salt);
		
		parent::add();
		
		$this->createSession();
	}
	
	public function getACL()
	{
		$acl = array();
		
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
		$stmt->execute(array($this->id,$this->id));
		foreach ($stmt as $v)
		{
			$acl[$v[0]] = $v[1];
		}
		return $acl;
	}
	
	public function ableTo($key)
	{
		if ($this->acl === null)
		{
			$this->acl = $this->getACL();
			$this->createSession();
		}
		if (isset($this->acl[self::ACL_OMNIPOTENT]) && $this->acl[self::ACL_OMNIPOTENT] > 0)
		{
			return true;
		}
		return isset($this->acl[$key]) && $this->acl[$key] > 0;
	}
	
	public function __sleep()
	{
		$r = parent::__sleep();
		$r[] = 'acl';
		return $r;
	}
}
?>