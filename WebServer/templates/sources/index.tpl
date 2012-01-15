{extends file="base.tpl"}
{block name="html_head" append}
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
<link rel='stylesheet' href='templates/index.css' />
{/block}
{block name="body"}
<div id="blocknav">
<div id="bn_banners">
<img src='templates/images/index/banner_1.jpg' /><img src='templates/images/index/banner_2.jpg' />
</div>


</div>
<div id="bn_links">
<a href='#' onclick='moveBannerTo(1);return false;'><img src='templates/images/index/bullet.png' /></a>
<a href='#' onclick='moveBannerTo(2);return false;'><img src='templates/images/index/bullet.png' /></a>
</div>
{if $user->id == 0}
<div class="homepage_box">
<div class="homepage_inner_box" id="user_panel">
   <div id="login_panel"><h2>Log in</h2>

<div id="login_traditional">
<table>
<form action="index.php?mod=user&act=login" method="post">
<tr><td>Username</td><td><input name="username" /></td></tr>
<tr><td>Password</td><td><input type="password" name="password" /></td></tr>
<tr><td>Remember?</td><td><input type="checkbox" name="remember" /></td></tr>
<tr><td colspan="2"><input type='submit' value='Submit' /></td></tr>
</form>
</table>
</div>
   </div>
   <div id="register_panel"><h2>Register</h2>
<table>
<form action="index.php?mod=user&act=register_submit" method="post">
<tr><td>Username</td><td><input name="username" onclick="showCAPTCHA()" /></td></tr>
<tr><td>Password</td><td><input type="password" name="password" /></td></tr>
<tr><td>Confirm</td><td><input type="password" name="password_confirm" /></td></tr>
<tr><td>Email</td><td><input name="email" /></td></tr>
<tr><td>Invitation</td><td><input name="invitation" /></td></tr>
<tr><td colspan="2"><input type='submit' value='Submit' /></td></tr>
</form>
</table>
</div>
</div>
</div>
{else}
<div class="homepage_box">
<div class="homepage_inner_box" id="quicksubmit">
<div id="drop_instructions">
<h2>Quick Submit</h2>
Submit by choosing source file or dragging your solution to the dropbox. Please indicate problem ID and use proper extension. Example: 1895.cpp, P3421.pas
<form>
<input id="submitsoln" type="file" name="source" />
</form>
</div>
<div id="dropzone"></div>
</div>
</div>
{/if}
{/block}