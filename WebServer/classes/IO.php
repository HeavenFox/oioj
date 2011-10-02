<?php
class IO
{
	public static function GET($prop, $defaultValue = null, $san = null)
	{
		return self::GetArrayElement($_GET, $prop, $defaultValue, $san);
	}
	
	public static function POST($prop, $defaultValue = null, $san = null)
	{
		return self::GetArrayElement($_GET, $prop, $defaultValue, $san);
	}
	
	public static function Session($prop, $defaultValue = null, $san = null)
	{
		return self::GetArrayElement($_SESSION, $prop, $defaultValue, $san);
	}
	
	public static function Cookie($prop, $defaultValue = null, $san = null)
	{
		return self::GetArrayElement($_GET, $prop, $defaultValue, $san);
	}
	
	public static function SetCookie($prop, $val)
	{
		$_COOKIE[$prop] = $val;
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