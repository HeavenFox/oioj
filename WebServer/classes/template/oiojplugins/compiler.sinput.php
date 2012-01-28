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
function smarty_compiler_sinput($params, Smarty $smarty)
{
    $id = $params['id'];
    unset($params['id']);
    $sub = null;
    if (isset($params['sub']))
    {
        $sub = $params['sub'];
        unset($params['sub']);
    }
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
        $paramstr .= $k;
        $paramstr .= ' => ';
        $paramstr .= $v;
    }
    $paramstr .= ')';
    return '<?php echo $_smartyform_obj->getHTML('.$id.','.($sub ? $sub : 'null').','.$paramstr.'); ?>';
}
?>