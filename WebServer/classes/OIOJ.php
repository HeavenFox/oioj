<?php
class OIOJ
{
	public static $template;
	public static $User;
	
	public static function InitTemplate()
	{
		import('template.Template');
		return self::$template = new Template();
	}
	
	public static function InitDatabase()
	{
		import('database.Database');
		Database::Get(Config::$MySQL);
	}
	
	public static function InitUser()
	{
		if (!self::$User)
		{
			self::$User = User::GetCurrentUser();
		}
	}
}
?>