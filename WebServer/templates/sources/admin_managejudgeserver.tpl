{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
<script type="text/javascript">
$(function(){
	$('.fancylink').fancybox();
});
</script>
{/block}
{block name="column-left"}
<div id="title_bar">
<h1>Manage Judge Server</h1>
</div>
<table class='tablist'>
<thead>
<tr><td>On?</td><td>ID</td><td>Name</td><td>IP : Port</td><td>workload</td><td>max. workload</td><td>FTP User</td><td></td></tr>
</thead>
<tbody>
{foreach $servers as $s}
<tr><td>{if $s->online}<span style='color: green'>Y</span>{else}<span style='color: red'>N</span>{/if}</td><td>{$s->id}</td><td>{$s->name}</td><td>{$s->ip}:{$s->port}</td><td>{$s->workload}</td><td>{$s->maxWorkload}</td><td>{$s->ftpUsername}</td><td><a href='index.php?mod=admin_judgeserver&amp;act=edit&amp;id={$s->id}'>[Edit]</a> <a href="index.php?mod=admin_judgeserver&amp;act=stats&amp;id={$s->id}" class="fancylink">[Status]</a> <a href="index.php?mod=admin_judgeserver&amp;act=sync&amp;id={$s->id}" class="fancylink">[Sync]</a> <a href="index.php?mod=admin_judgeserver&amp;act=ping&amp;id={$s->id}" class="fancylink">[Ping]</a></td></tr>
{/foreach}
</tbody>
</table>
<a href='index.php?mod=admin_judgeserver&amp;act=add'>Add Server</a>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}