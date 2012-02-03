{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
<script type="text/javascript" src='scripts/permissions.js'></script>
<script type="text/javascript">
$(function(){
	setColor();
});
</script>
{/block}
{block name="column-left"}
<div id='title_bar' class="with_secondline">
<div class="secondline"><h2>View Tag Permissions</h2><div>To edit, click on a value</div></div>
</div>
<table class='tablist'>
<thead>
<tr><td>Key Name</td>
{foreach $tags as $t}
<td>{$t->tag}</td>
{/foreach}
</tr>
</thead>
<tbody>
{foreach $table as $row}
<tr><td>{$row.name}</td>
	{foreach $row.tag_perms as $k => $p}
	<td class="perm_num"><a href="javascript:;" onclick="editTagPerm(this,false)" data-key="{$row.key|escape}" data-tid="{$tags[$k]->id}">{$p}</a></td>
	{/foreach}
</tr>
{/foreach}
</tbody>
</table>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}