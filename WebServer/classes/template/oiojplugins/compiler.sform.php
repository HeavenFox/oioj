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
    return '<?php $_smartyform_obj = '.$params['obj'].'; echo $_smartyform_obj->getFormOpeningHTML(); ?>';
}
?>