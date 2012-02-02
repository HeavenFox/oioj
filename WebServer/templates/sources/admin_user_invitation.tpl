{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
{/block}
{block name="column-left"}
<div id='title_bar'>
<h2>Manage Invitations</h2>
</div>
<table class='tablist'>
<thead>
<tr><td>Code</td><td>Used by</td></tr>
</thead>
<tbody>
{foreach $invitations as $inv}
<tr><td>{$inv->code}</td><td>{if $inv->user->username}{$inv->user->username}{else}None{/if}</td></tr>
{/foreach}
</tbody>
</table>
<form method="post" action='index.php?mod=admin_user&amp;act=invitation&amp;do=generate'>Generate <input type='number' name='count' /><input type='submit' /></form>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}