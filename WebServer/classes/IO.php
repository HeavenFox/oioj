<?php
class IO
{
	public static function GET($prop, $defaultValue = null, $san = null)
	{
		return self::GetArrayElement($_GET, $prop, $defaultValue, $san);
	}
	
	public static function POST($prop, $defaultValue = null, $san = null)
	{
		return self::GetArrayElement($_POST, $prop, $defaultValue, $san);
	}
	
	public static function Session($prop, $defaultValue = null, $san = null)
	{
		return self::GetArrayElement($_SESSION, $prop, $defaultValue, $san);
	}
	
	public static function Cookie($prop, $defaultValue = null, $san = null)
	{
		return self::GetArrayElement($_COOKIE, $prop, $defaultValue, $san);
	}
	
	public static function SetCookie($prop, $val, $expire)
	{
		setcookie($prop,$val,time()+$expire);
	}
	
	public static function DestroySession($prop = null)
	{
		if ($prop)
		{
			unset($_SESSION[$prop]);
		}
		else
		{
			session_destroy();
			unset($_SESSION);
		}
	}
	
	public static function SetSession($prop, $val)
	{
		$_SESSION[$prop] = $val;
	}
	
	
	private static function GetArrayElement(&$ar, $prop, $defaultValue, $san)
	{
		if (!isset($ar[$prop])) {
			return $defaultValue;
		}
		if ($san) {
			return $san($ar[$prop]);
		} else {
			return $ar[$prop];
		}
	}
}
?>