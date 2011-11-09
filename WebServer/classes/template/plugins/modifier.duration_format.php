<?php
function smarty_modifier_duration_format($string, $format = null, $default_date = '',$formatter='auto')
{
	$dur = intval($string);
	$res = '';
    $s = $dur % 60;
    $res = $s.'s'.$res;
    $dur /= 60;
    $min = $dur%60;
    if ($min)
    {
    	$res = $min.'min '.$res;
    }
    $dur /= 60;
    if ($dur)
    {
    	$res = $dur.'h '.$res;
    }
	return $res;
} 

?>