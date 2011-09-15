<?php
define('IN_OIOJ', 1);
define('ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR);

require_once ROOT . DIRECTORY_SEPARATOR . 'config.php';

/**
 * Import class
 * @param string $package Class package to import
 */
function import($package)
{
    $url = str_replace('.', DIRECTORY_SEPARATOR, $package);
    
    require_once ROOT . 'classes' . DIRECTORY_SEPARATOR . $url . '.php';
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
    