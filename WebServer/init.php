<?php
define('IN_OIOJ', 1);
define('ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR);
define('MODULE_DIR',ROOT.'modules'.DIRECTORY_SEPARATOR);
define('LIB_DIR',ROOT.'lib'.DIRECTORY_SEPARATOR);

require_once ROOT . DIRECTORY_SEPARATOR . 'config.php';

define('OIOJ_VERSION', 100);
define('OIOJ_READABLE_VERSION','v1.0.0');

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

// Add fallback mechanism
/*
define('SMARTY_SPL_AUTOLOAD',1);
spl_autoload_register(function($className){
	import($className);
});*/
?>