{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
{sheader obj=$sf_settings}
{/block}
{block name="column-left"}
<div id='title_bar'>
<div><h2>Settings</h2></div>
</div>
{sform obj=$sf_settings}
<table class='tablist'>
<thead>
<tr><td colspan="2">Site Settings</td>
</tr>
</thead>
<tbody>
<tr>
<td>{slabel id='tmp_dir'}</td><td>{sinput id='tmp_dir'}</td>
</tr>
<tr>
<td>{slabel id='default_timezone'}</td><td>{sinput id='default_timezone'}</td>
</tr>
</tbody>
<thead>
<tr><td colspan="2">Judging Settings</td>
</tr>
</thead>
<tbody>
<tr>
<td>{slabel id='token'}</td><td>{sinput id='token'}</td>
</tr>
<tr>
<td>{slabel id='backup_token'}</td><td>{sinput id='backup_token'}<br /><small>Backup Token is used when you are changing Token and do not wish to interrupt. Put old token here and it will be accepted. Once you changed token for every server, remove it.</small></td>
</tr>
<tr>
<td>{slabel id='local_judgeserver_data_dir'}</td><td>{sinput id='local_judgeserver_data_dir'}</td>
</tr>
</tbody>
<thead>
<tr><td colspan="2">CAPTCHA</td>
</tr>
</thead>
<tbody>
<tr>
<td>{slabel id='recaptcha_public'}</td><td>{sinput id='recaptcha_public'}</td>
</tr>
<tr>
<td>{slabel id='recaptcha_private'}</td><td>{sinput id='recaptcha_private'}<br /></td>
</tr>
</tbody>
<thead>
<tr><td colspan="2">User</td>
</tr>
</thead>
<tbody>
<tr>
<td>{slabel id='user_default_tags'}</td><td>{sinput id='user_default_tags'}</td>
</tr>
<tr>
</tr>
</tbody>
</table>
<input type='submit' value='Submit' />
{/sform}
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}