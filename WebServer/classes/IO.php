<?php
class IO
{
	public static function GET($prop, $default = null, $san = null)
	{
		
	}
	
	private static function GetArrayElement(&$ar, $prop, $defaultValue, $san)
	{
		if (!isset($ar($prop)))
		{
			return $defaultValue;
		}
		
	}
}
?>