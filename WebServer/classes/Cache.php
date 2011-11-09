<?php
class Cache
{
	/*-------------------------------------
	   MASTER SWITCH OF CACHING.
	 --------------------------------------*/
	private static $Enabled = true;
	
	//-------------------------------------
	// Memory Cacheing
	//  These are used for globally persistent files
	//-------------------------------------
	
	public static function MemAvailable()
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
		return false;
	}
	
	public static function MemGet($id)
	{
		if (function_exists('apc_fetch'))
		{
			return apc_fetch($id);
		}
		
		return null;
	}
	
	//--------------------------------------
	// User Caching
	//  These are stored in session
	//--------------------------------------
	
	public static function UserGet($id)
	{
		
	}
	
	public static function UserSet($id, $vari)
	{
		
	}
}
?>