<?php
/**
 * Smarty plugin to execute PHP code
 * 
 * @package Smarty
 * @subpackage PluginsBlock
 * @author Uwe Tews 
 */

/**
 * Smarty {php}{/php} block plugin
 * 
 * @param string $content contents of the block
 * @param object $template template object
 * @param boolean $ &$repeat repeat flag
 * @return string content re-formatted
 */
function smarty_block_ckeditor($params, $content, $template, &$repeat)
{
	require_once ROOT.'lib/ckeditor_php5.php';
    return '';
}

?>