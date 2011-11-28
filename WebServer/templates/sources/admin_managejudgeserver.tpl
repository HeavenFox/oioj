{extends file="two-column.tpl"}
{block name="extra_header" prepend}
<script type="text/javascript" src="scripts/admin_addproblem.js"></script>
<script type="text/javascript" src="scripts/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="scripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="scripts/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
{/block}
{block name="column-left"}
<table>
</table>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}