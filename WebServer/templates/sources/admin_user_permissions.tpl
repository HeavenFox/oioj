{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
{/block}
{block name="column-left"}
<h2>View Permission</h2>
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
<tr><td>{$row.name}</td><td>{array_sum($row.perms)}</td>
	{foreach $row.perms as $p}
	<td>{$p}</td>
	{/foreach}
</tr>
{/foreach}
</tbody>
</table>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}