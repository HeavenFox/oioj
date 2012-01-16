<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     compiler.ifable.php
 * Type:     compiler
 * Name:     ifable
 * Purpose:  If current user has permission to do sth
 * -------------------------------------------------------------
 */
function smarty_compiler_ifable($params, Smarty $smarty)
{
    return "<?php if (User::GetCurrent()->ableTo('".$params['to']."')) { ?>";
}
?>