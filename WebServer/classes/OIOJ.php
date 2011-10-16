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
	
	public static function Redirect($message, $redirect = null)
	{
		if (!$redirect)
		{
			$redirect = $_SERVER['HTTP_REFERER'];
		}
		
		self::$template->assign('message',$message);
		self::$template->assign('redirect',$redirect);
		self::$template->display('redirect.tpl');
	}
}
?>