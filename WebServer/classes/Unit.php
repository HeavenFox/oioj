<?php
class Unit
{
	private static $constants = array(
		
		'hours' => 3600,
		'hrs' => 3600,
		'hour' => 3600,
		'min' => 60,
		's' => 1,
		'ms' => 0.001,
			
		// size
		'gb' => 1073741824, // 1024 * 1024 * 1024
		'mb' => 1048576, // 1024 * 1024
		'kb' => 1024
	
	);
	
	public static function Convert($value, $from, $to = null)
	{
		$value *= self::$constants[strtolower($from)];
		if ($to)
		{
			$value /= self::$constants[strtolower($to)];
		}
		return $value;
	}
}
?>