{extends file="base.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/index.css' />
<script type="text/javascript" src="scripts/index.js"></script>
<script type="text/javascript" src="scripts/popup_record.js"></script>
<script type="text/javascript" src="scripts/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="scripts/jquery.fileupload.js"></script>
<script type="text/javascript" src="scripts/submitsoln/submitsoln.js"></script>
<script type="text/javascript" src="scripts/submitsoln/submitsoln_prob.js"></script>
<script type="text/javascript">
$(function(){
	$("#submitsoln").submitsoln_prob({
		drop: $('#dropzone'),
		inClass: 'in',
		hoverClass: 'hover'
	});
});
</script>
{/block}
{block name="body"}
<div id="blocknav">
<div id="bn_banners">
<img src='templates/images/index/banner_1.jpg' alt='First Math Tournament' /><img src='templates/images/index/banner_2.jpg' alt='First AI Contest' />
</div>
</div>
<div id="bn_links">
<a href='#' onclick='moveBannerTo(1);return false;'><img src='templates/images/index/bullet.png' alt='Banner 1' /></a>
<a href='#' onclick='moveBannerTo(2);return false;'><img src='templates/images/index/bullet.png' alt='Banner 2' /></a>
</div>
{if $user->id == 0}
<div class="homepage_box">
<div class="homepage_inner_box" id="user_panel">
   <div id="login_panel"><h2>Log in</h2>

<div id="login_traditional">
{sform obj=$sf_login}
<table>
<tr><td>{slabel id="username"}</td><td>{sinput id="username"}</td></tr>
<tr><td>{slabel id="password"}</td><td>{sinput id="password"}</td></tr>
<tr><td colspan="2">{sinput id="remember"}{slabel id="remember"}</td></tr>
<tr><td colspan="2"><input type='submit' value='Submit' /></td></tr>
</table>
{/sform}
</div>
   </div>
   <div id="register_panel"><h2>Register</h2>
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
</div>
</div>
</div>
{else}
<div class="homepage_box">
<div class="homepage_inner_box" id="quicksubmit">
<div id="drop_instructions">
<h2>Quick Submit</h2>
<p>Submit by choosing source file or dragging your solution to the dropbox. Please indicate problem ID and use proper extension. Example: 1895.cpp, P3421.pas</p>
<p>Or, you can paste your code to <a href='index.php?mod=submit'>Submission Form</a></p>
<form>
<input id="submitsoln" type="file" name="source" />
</form>
</div>
<div id="dropzone"></div>
</div>
</div>
{/if}
{/block}