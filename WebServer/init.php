<?php
define('IN_OIOJ', 1);
define('ROOT',basename(__FILE__).DIRECTORY_SEPARATOR);

/**
 * Import class
 * @param string $package Class package to import
 */
function import($package)
{
    str_replace('.', DIRECTORY_SEPARATOR, $package);
    
    require_once ROOT . 'classes' . DIRECTORY_SEPARATOR . $package;
}

/**
 * Parse general protocol
 * @param string $str protocol data
 * @return array
 */
function parseProtocol($str)
{
	$a = explode("\n", $str);
	$t = array();
	foreach ($a as $v)
	{
		$i = strpos($v, ' ');
		$t[substr($v, 0, $i)] = substr($v, $i+1);
	}
	return $t;
}
?>
    