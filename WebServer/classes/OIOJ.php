<?php
class OIOJ
{
	public static $template;
	
	public static function InitTemplate()
	{
		return self::$template = new Template();
	}
	
	public static function PrepareDatabase()
	{
		import('database.Database');
		Database::Get(Config::$MySQL);
	}
	
}
?>