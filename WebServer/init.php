<?php
define('IN_OIOJ', 1);
define('ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR);
define('MODULE_DIR',ROOT.'modules'.DIRECTORY_SEPARATOR);
define('LIB_DIR',ROOT.'lib'.DIRECTORY_SEPARATOR);
define('VAR_DIR',ROOT.'vars'.DIRECTORY_SEPARATOR);

define('OIOJ_VERSION', 90);
define('OIOJ_READABLE_VERSION','v1.0.0 Beta');

// Turn off magic quotes
if (get_magic_quotes_gpc())
{
    function stripslashes_gpc(&$value)
    {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

/**
 * Import class
 * @param string $package Class package to import
 */
function import($package)
{
    $url = str_replace('.', DIRECTORY_SEPARATOR, $package);
    
    require_once ROOT . 'classes' . DIRECTORY_SEPARATOR . $url . '.php';
}

function loadVar($data)
{
	include VAR_DIR.$data.'.php';
	return $$data;
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