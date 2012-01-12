<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     compiler.else.php
 * Type:     compiler
 * Name:     else
 * Purpose:  End tag for ifallows or ifdenies
 * -------------------------------------------------------------
 */
function smarty_compiler_else($params, Smarty $smarty)
{
    return "<?php } else { ?>";
}
?>