<?php
class OIOJ
{
	/**
	 * Template Object
	 *
	 * @var Template
	 */
	public static $template;
	
	private static $breadcrumbHandlerAdded = false;
	
	public static $breadcrumb = array();
	
	public static function InitTemplate()
	{
		import('template.Template');
		return self::$template = new Template();
	}
	
	public static function InitDatabase()
	{
		import('database.Database');
		$params = @loadVar('DBParameters');
		if (!$params)
		{
			throw new DBNotConfiguredException();
		}
		Database::Get($params);
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
	
	public static function GlobalMessage($message)
	{
		OIOJ::$template->assign('global_message',$message);
	}
	
	public static function AddBreadcrumb($breadcrumb, $url = NULL)
	{
		if (!self::$breadcrumbHandlerAdded)
		{
			self::$breadcrumbHandlerAdded = true;
			self::$template->addDisplayHandler(function(){
				OIOJ::$template->assign('breadcrumb',OIOJ::$breadcrumb);
			});
		}
		
		if (is_string($breadcrumb))
		{
			$breadcrumb = array($breadcrumb => $url);
		}
		self::$breadcrumb = array_merge(self::$breadcrumb, $breadcrumb);
	}
}

class PermissionException extends Exception
{
	public $key;
	public function __construct($key = NULL)
	{
		$this->key = $key;
		parent::__construct('Access Denied');
	}
}

class InputException extends Exception
{
	
}


class DBNotConfiguredException extends Exception
{
}

?>