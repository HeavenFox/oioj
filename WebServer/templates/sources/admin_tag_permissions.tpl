{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
<script type="text/javascript" src='scripts/permissions.js'></script>
<script type="text/javascript">
function setTagProperties(obj)
{
	var newState = obj.checked;
	// Do not change value now...
	obj.checked = !newState;
	$.post("index.php?mod=admin_user&act=tagproperties",{ key: $(obj).data('key'), tid: $(obj).data('tid'), state: (newState?1:0) },function(data)
	{
		console.log(data);
		obj.checked = newState;
	});
}

$(function(){
	setColor();
});
</script>
{/block}
{block name="column-left"}
<div id='title_bar' class="with_secondline">
<div class="titles"><h1>View Tag Permissions</h1><div>To edit, click on a value</div></div>
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
	{if isset($row.tag_perms)}
	{foreach $row.tag_perms as $k => $p}
	<td class="perm_num"><a href="javascript:;" onclick="editTagPerm(this,false)" data-key="{$row.key|escape}" data-tid="{$tags[$k]->id}">{$p}</a></td>
	{/foreach}
	{/if}
</tr>
{/foreach}
</tbody>
<thead>
	<tr><td>Tag Properties</td>
	{foreach $tags as $t}
	<td>{$t->tag}</td>
	{/foreach}
	</tr>
</thead>
<tbody>
	<tr><td>Auto Apply to New Users?</td>
	{foreach $properties as $k => $t}	
	<td><input type="checkbox" {if isset($t.auto_apply_new_user)}checked="checked"{/if} onchange="setTagProperties(this)" data-key="auto_apply_new_user" data-tid="{$tags[$k]->id}" /></td>
	{/foreach}</tr>
	<tr><td>Users Can Freely Join?</td>
	{foreach $properties as $k => $t}	
	<td><input type="checkbox" {if isset($t.freely_join)}checked="checked"{/if} onchange="setTagProperties(this)" data-key="freely_join" data-tid="{$tags[$k]->id}" /></td>
	{/foreach}</tr>
</tbody>
</table>
<form method="post" action="index.php?mod=admin_user&amp;act=tagpermissions">
New/Other Tag: <input name="tag" /><input type="submit" value="Submit" />
</form>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}