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
		
		$tags = explode(';',Settings::Get('user_default_tags'));
		foreach ($tags as $k => $tagid)
		{
			$tagid = intval($tagid);
			$tags[$k] = "({$this->id},{$tagid})";
		}
		
		Database::Get()->query('INSERT INTO `oj_user_tags` (`uid`,`tid`) VALUES '.implode(',',$tags));
		
		$this->createSession();
	}
	
	public function getACL()
	{
		$acl = array();
		
		$str = 'SELECT  `key` , SUM( `permission`) FROM (
SELECT  `key` ,  `permission` 
FROM  `oj_user_acl` WHERE `uid` = ?
UNION ALL 
SELECT  `key` ,  `permission` 
FROM  `oj_tag_acl` LEFT JOIN `oj_user_tags` USING (tid) WHERE `oj_user_tags`.`uid` = ?
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
	
	public function unableTo($key)
	{
		if ($this->acl === null)
		{
			$this->acl = $this->getACL();
			$this->createSession();
		}
		if (isset($this->acl[self::ACL_OMNIPOTENT]) && $this->acl[self::ACL_OMNIPOTENT] > 0)
		{
			return false;
		}
		return isset($this->acl[$key]) && $this->acl[$key] < 0;
	}
	
	public function __sleep()
	{
		$r = parent::__sleep();
		$r[] = 'acl';
		return $r;
	}
}
?>