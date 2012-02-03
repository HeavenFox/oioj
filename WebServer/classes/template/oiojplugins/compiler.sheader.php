<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     compiler.sheader.php
 * Type:     compiler
 * Name:     sheader
 * Purpose:  Print SmartyForm Header
 * -------------------------------------------------------------
 */
function smarty_compiler_sheader($params, Smarty $smarty)
{
	return '<?php echo '.$params['obj'].'->getHeaderHTML(); ?>';
}
?>