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
function smarty_compiler_slabel($params, Smarty $smarty)
{
    return '<?php echo $_smartyform_obj->getLabelHTML('.$params['id'].'); ?>';
}
?>