<?php
import('Cache');
class Settings
{
	private static $stored = null;
	public static function Get($param)
	{
		if (!self::$stored)
		{
			$p = Cache::MemGet('settings');
			if (!$p)
			{
				$p = array();
				$db = Database::Get();
				$stmt = $db->query('SELECT `key`,`value` FROM `oj_settings`');
				foreach ($stmt as $r)
				{
					$p[$r['key']] = $r['value'];
				}
				Cache::MemSet('settings',$p);
				self::$stored = $p;
			}
			else
			{
				self::$stored = $p;
			}
		}
		return self::$stored[$param];
	}
	
	public static function Flush()
	{
		Cache::MemFlush('settings');
		self::$stored = null;
	}
}
?>