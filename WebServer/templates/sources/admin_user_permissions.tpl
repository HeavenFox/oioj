{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
<script type="text/javascript" src='scripts/permissions.js'></script>
<script type="text/javascript">
$(function(){
	setColor();calculateSum();
});
</script>
{/block}
{block name="column-left"}
<div id='title_bar' class="with_secondline">
<h2>View Permission: {$user->username}</h2><div class="secondline">To edit, click on a value</div>
</div>
<table class='tablist'>
<thead>
<tr><td>Key Name</td><td>Effective</td><td>User-Specific</td>
{foreach $tags as $t}
<td>{$t->tag}</td>
{/foreach}
</tr>
</thead>
<tbody>
{foreach $table as $row}
<tr><td>{$row.name}</td><td class="perm_num perm_sum_num"></td><td class="perm_num"><a href='javascript:;' onclick='editUserPerm(this)' data-key='{$row.key|escape}' data-uid='{$user->id}'>{$row.user_perms}</a></td>
	{if isset($row.tag_perms)}
	{foreach $row.tag_perms as $k => $p}
	<td class="perm_num"><a href="javascript:;" onclick="editTagPerm(this,true)" data-key="{$row.key|escape}" data-tid="{$tags[$k]->id}">{$p}</a></td>
	{/foreach}
	{/if}
</tr>
{/foreach}
</tbody>
</table>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}