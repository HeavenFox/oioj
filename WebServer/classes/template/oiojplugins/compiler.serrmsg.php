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
function smarty_compiler_serrmsg($params, Smarty $smarty)
{
    return '<?php echo $_smartyform_obj->getLabelHTML('.isset($params['id'] ? $params['id'] : ''.'); ?>';
}
?>