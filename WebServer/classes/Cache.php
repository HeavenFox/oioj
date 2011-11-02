<?php
class Cache
{
	/*-------------------------------------
	   MASTER SWITCH OF CACHING.
	 --------------------------------------*/
	private static $Enabled = true;
	
	public static function Available()
	{
		if (!self::$Enabled)return false;
		$func = array('apc_store');
	}
	
	public static function MemSet($id, $vari)
	{
		if (function_exists('apc_store'))
		{
			apc_store($id,$vari);
		}
	}
	
	public static function MemGet($id)
	{
		if (function_exists('apc_fetch'))
		{
			return apc_fetch($id);
		}
		
		return null;
	}
}
?>