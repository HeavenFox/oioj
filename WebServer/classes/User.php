<?php
class User extends ActiveRecord
{
	public $id;
	public $name;
	public $email;
	public $tags;
	
	public $submission;
	public $acceptance;
	
	public static function GetCurrentUser()
	{
		if (IO::Session('uid'))
		{
			
		}
		
		if (IO::Cookie('uid'))
		{
			// Log in automatically
		}
		
		return null;
	}
	
	public function getACL()
	{
		'SELECT  `key` , SUM( `permission`) 
FROM (

SELECT  `key` ,  `permission` 
FROM  `oj_users_acl` 
UNION ALL 
SELECT  `key` ,  `permission` 
FROM  `oj_tags_acl`
)
GROUP BY  `key`'
	}
}
?>