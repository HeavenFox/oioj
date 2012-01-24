<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     compiler.endif.php
 * Type:     compiler
 * Name:     endif
 * Purpose:  End tag for ifallows or ifdenies
 * -------------------------------------------------------------
 */
function smarty_compiler_sformclose($params, Smarty $smarty)
{
    return "</form>";
}
?>