<?php
class Cache
{
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