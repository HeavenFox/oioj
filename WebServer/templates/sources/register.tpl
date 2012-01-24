{extends file="base.tpl"}
{block name="body"}
<h2>Register</h2>
{sform obj=$sf_register}
<table>
<tr><td>{slabel id="username"}</td><td>{sinput id="username"}</td></tr>
<tr><td>{slabel id="password"}</td><td>{sinput id="password"}</td></tr>
<tr><td>{slabel id="password_confirm"}</td><td>{sinput id="password_confirm"}</td></tr>
<tr><td>{slabel id="email"}</td><td>{sinput id="email"}</td></tr>
<tr><td>{slabel id="invitation"}</td><td>{sinput id="invitation"}</td></tr>
<tr><td colspan="2"><input type='submit' value='Submit' /></td></tr>
</table>
{/sform}
{/block}