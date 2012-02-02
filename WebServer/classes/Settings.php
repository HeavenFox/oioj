<?php
import('Cache');
class Settings
{
	private static $stored = null;
	public static function Get($param)
	{
		if (!self::$stored)
		{
			self::StoreSettings();
		}
		return self::$stored[$param];
	}
	
	public static function Set($param, $val)
	{
		$stmt = Database::Get()->prepare('UPDATE `oj_settings` SET `'.$param.'` = ?');
		$stmt->execute(array($val));
	}
	
	public static function GetAll()
	{
		return self::$stored;
	}
	
	private static function StoreSettings()
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
		}
		self::$stored = $p;
	}
	
	public static function Flush()
	{
		Cache::MemFlush('settings');
		self::$stored = null;
	}
}
?>