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
    return '<?php echo $_smartyform_obj->getHTML('.$params['id'].'); ?>';
}
?>