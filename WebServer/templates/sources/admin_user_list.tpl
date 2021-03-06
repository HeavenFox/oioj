{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
<link rel='stylesheet' href='templates/pager.css' />
{/block}
{block name="column-left"}
<div id='title_bar'>
<h1>Manage Users</h1>
</div>
<table class='tablist'>
<thead>
<tr><td>ID</td><td>Username</td><td>Actions</td></tr>
</thead>
<tbody>
{foreach $users as $user}
<tr><td>{$user->id}</td><td>{$user->username}</td><td>[Edit] <a href="index.php?mod=admin_user&act=permissions&uid={$user->id}">[Permissions]</a>{ifable to="omnipotent"} <a href='index.php?mod=admin_user&act=loginas&id={$user->id}'>[Login as]</a>{endif}</td></tr>
{/foreach}
</tbody>
</table>
{pager cur=$page_cur max=$page_max url="index.php?mod=problemlist&page=%d" form="index.php?mod=problemlist" var="page"}{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}