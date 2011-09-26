<?php
class OIOJ
{
	public static $template;
	
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
	
}
?>