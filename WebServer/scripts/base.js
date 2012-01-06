function globalShowLoginBox()
{
	$.fancybox('<div id="global-login-box"><h2>Log in to OIOJ</h2>	<table>	<form action="index.php?mod=user&act=login" method="post">	<tr><td>Username</td><td><input name="username" /></td></tr>	<tr><td>Password</td><td><input type="password" name="password" /></td></tr>	<tr><td>Remember?</td><td><input type="checkbox" name="remember" /></td></tr>	<tr><td colspan="2"><input type="submit" value="Submit" /></td></tr>	</form>	</table></div>',{
		'scrolling': 'no',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic'
	});
}