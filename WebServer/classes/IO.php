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
	
	public static function REQUEST($prop, $defaultValue = null, $san = null)
	{
		return self::GetArrayElement($_REQUEST, $prop, $defaultValue, $san);
	}
	
	public static function Session($prop, $defaultValue = null, $san = null)
	{
		return self::GetArrayElement($_SESSION, $prop, $defaultValue, $san);
	}
	
	public static function Cookie($prop, $defaultValue = null, $san = null)
	{
		return self::GetArrayElement($_COOKIE, $prop, $defaultValue, $san);
	}
	
	public static function StartSession()
	{
		session_start();
	}
	
	public static function SetCookie($prop, $val, $expire)
	{
		setcookie($prop,$val,time()+$expire);
	}
	
	public static function SetSecureCookie($prop, $val, $expire)
	{
		
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
			$_SESSION = array();
			if (ini_get("session.use_cookies"))
			{
			    $params = session_get_cookie_params();
			    setcookie(session_name(), '', time() - 42000,
			        $params["path"], $params["domain"],
			        $params["secure"], $params["httponly"]
			    );
			}
		}
	}
	
	public static function SetSession($prop, $val)
	{
		$_SESSION[$prop] = $val;
	}
	
	/**
	 * Ping any IP Address
	 * It has been proved unfeasible to ping directly from php (raw), thus we use exec
	 */
	public static function Ping($ip, $count = 4, $timeout = 500)
	{
		exec('ping -c '.escapeshellarg($count).' -W '.escapeshellarg($timeout).' '.escapeshellarg($ip),$result,$a);
		return implode("\n", $result);
	}
	
	
	public static function GetArrayElement(&$ar, $prop, $defaultValue = null, $san = null)
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