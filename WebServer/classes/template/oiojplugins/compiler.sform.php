<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     compiler.sform.php
 * Type:     compiler
 * Name:     sform
 * Purpose:  SmartyForm
 * -------------------------------------------------------------
 */
function smarty_compiler_sform($params, Smarty $smarty)
{
	$obj = $params['obj'];
	unset($params['obj']);
	$paramstr = 'array(';
    $first = true;
    foreach ($params as $k => $v)
    {
        if ($first)
        {
            $first = false;
        }else
        {
            $paramstr .= ',';
        }
        $paramstr .= "'{$k}'";
        $paramstr .= ' => ';
        $paramstr .= $v;
    }
    $paramstr .= ')';
    return '<?php $_smartyform_obj = '.$obj.'; echo $_smartyform_obj->getFormOpeningHTML('.$paramstr.'); ?>';
}
?>