<?php
function smarty_modifier_duration_format($string, $format = null, $default_date = '',$formatter='auto')
{
	$dur = intval($string);
	$res = '';
    $s = $dur % 60;
    $res = $s;
    $dur = floor($dur / 60);
    $min = $dur % 60;
    $res = $min.':'.$res;
    $dur = floor($dur / 60);
    $res = $dur.':'.$res;
    
	return $res;
} 

?>