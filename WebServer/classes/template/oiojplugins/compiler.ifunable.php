<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     compiler.ifunable.php
 * Type:     compiler
 * Name:     ifunable
 * Purpose:  If current user doesn't has permission to do sth
 * -------------------------------------------------------------
 */
function smarty_compiler_ifunable($params, Smarty $smarty)
{
    return "<?php if (User::GetCurrent()->unableTo(".$params['to'].")) { ?>";
}
?>