{extends file="two-column.tpl"}
{block name="html_head" append}
{/block}
{block name="column-left"}
<h2>{if isset($server)}Edit{else}Add{/if} Judge Server</h2>
<form action="index.php?mod=admin_judgeserver&amp;act=save" method="post">
{if isset($server)}
<input type="hidden" name="id" value="{$server->id}" />
{/if}
<table>
<tr>
	<td>Online</td>
	<td><input type='checkbox' name='online' {if isset($server) && $server->online}checked{/if}/></td>
</tr>
<tr>
	<td>Name</td>
	<td><input type='text' name='name' {if isset($server)}value="{$server->name}"{/if} /></td>
</tr>
<tr>
	<td>IP</td>
	<td><input type='text' name='ip' {if isset($server)}value="{$server->ip}"{/if} /></td>
</tr>
<tr>
	<td>Port Number</td>
	<td><input type='number' name='port' {if isset($server)}value="{$server->port}"{/if}/></td>
</tr>
<tr>
	<td>Maximum Workload</td>
	<td><input type='number' name='max_workload' {if isset($server)}value="{$server->maxWorkload}"{/if}/></td>
</tr>
<tr>
	<td>FTP Username</td>
	<td><input type='text' name='ftp_username' {if isset($server)}value="{$server->ftpUsername}"{/if}/><br /><small>Leave blank for local server</small></td>
</tr>
<tr>
	<td>FTP Password</td>
	<td><input type='password' name='ftp_password' />{if isset($server)}<br /><small>Leave blank not to change</small>{/if}</td>
</tr>
</table>
<input type='submit' value='Submit' />
</form>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}