<?php
define('IN_OIOJ', 1);
define('ROOT',basename(__FILE__).DIRECTORY_SEPARATOR);

function import($package)
{
    str_replace('.', DIRECTORY_SEPARATOR, $package);
    
    require_once ROOT . 'classes' . DIRECTORY_SEPARATOR . $package;
}
?>
    