{extends file="base.tpl"}
{block name="extra_header"}
<script type="text/javascript" src="scripts/index.js"></script>
<script type="text/javascript" src="scripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="scripts/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="scripts/jquery.fileupload.js"></script>
<script type="text/javascript" src="scripts/submitsoln/submitsoln.js"></script>
<script type="text/javascript">
$(function(){
	$("#submitsoln").submitsoln({
		drop: $('#dropzone')
	});
});
</script>
<link rel='stylesheet' href='templates/index.css' />
<link rel='stylesheet' href='scripts/fancybox/jquery.fancybox-1.3.4.css' />
{/block}
{block name="body"}
<div id="blocknav">
<div id="bn_banners">
<p><img src='templates/images/index/learn_banner.png' /></p>
<p><img src='templates/images/index/solve_banner.png' /></p>
<p><img src='templates/images/index/compete_banner.png' /></p>
</div>
<div id="bn_links">
<p><a href='#' onclick='moveBannerTo("-0")'><img src='templates/images/index/learn_tn.png' /></a></p>
<p><a href='#' onclick='moveBannerTo("-360px")'><img src='templates/images/index/solve_tn.png' /></a></p>
<p><img src='templates/images/index/compete_tn.png' /></p>
</div>

</div>
{if $user->id == 0}
<div id="user_panel">
   <div id="login_panel"><h2>Log in</h2>

<div id="login_traditional">
<p>Use OIOJ account | <a href="javascript:;" >Use OpenID (Google, Yahoo, etc)</a></p>
<table>
<form action="index.php?mod=user&act=login" method="post">
<tr><td>Username</td><td><input name="username" /></td></tr>
<tr><td>Password</td><td><input type="password" name="password" /></td></tr>
<tr><td>Remember?</td><td><input type="checkbox" name="remember" /></td></tr>
<tr><td colspan="2"><input type='submit' value='Submit' /></td></tr>
</form>
</table>
</div>
<div id="login_openid">
<p><a href="javascript:;" >Use OIOJ account</a> | Use OpenID (Google, Yahoo, etc)</p>
<p></p>
</div>
   </div>
   <div id="vertical_separator"></div>
   <div id="register_panel"><h2>Register</h2>
<p>If you have any OpenID (eg. Google Account, Yahoo account, etc), you do not need to register.</p>
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
{else}
<div id="quicksubmit">
<h2>Quick Submit</h2>
Submit by choosing source file or dragging your solution to the dropbox below. Please indicate problem ID and use proper extension. Example: 1895.cpp, P3421.pas
<form>
<input id="submitsoln" type="file" name="source" />
</form>
<div id="dropzone"></div>
</div>
{/if}
{/block}