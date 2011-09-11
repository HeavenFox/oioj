<?php

function import($package)
{
    $slash = '/';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
    {
        $slash = '\\';
    }
    
    str_replace('.', $slash, $package);
    
    require_once ROOT . 
}
?>
    