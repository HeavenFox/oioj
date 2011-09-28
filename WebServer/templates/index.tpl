{extends file="base.tpl"}
{block name="extra_header"}
<link rel='stylesheet' href='templates/index.css' />
{/block}
{block name="body"}
<div id="blocknav"><ul>
  <li id="learn"><img src="templates/images/index/learn.gif" /><h3><a href="learning.php">Learn</a></h3></li>
  <li id="solve"><img src="templates/images/index/learn.gif" /><h3><a href="problems.php">Solve</a></h3></li>
  <li id="compete"><img src="templates/images/index/learn.gif" /><h3><a href="contests.php">Compete</a></h3></li>
</ul>
</div>
<div id="user_panel">
   <div id="login_panel"><h2>Log in</h2>

<div id="login_traditional">
<p>Use OIOJ account | <a href="javascript:;" >Use OpenID (Google, Yahoo, etc)</a></p>
<table>
<form action="">
<tr><td>Username</td><td><input name="username" /></td></tr>
<tr><td>Password</td><td><input name="password" /></td></tr>
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
<form action="">
<tr><td>Username</td><td><input name="username" /></td></tr>
<tr><td>Password</td><td><input name="password" /></td></tr>
<tr><td>Confirm</td><td><input name="password_confirm" /></td></tr>
<tr><td>Email</td><td><input name="email" /></td></tr>
<tr><td>Code</td><td></td></tr>
<tr><td colspan="2"><input type='submit' value='Submit' /></td></tr>
</form>
</table>
</div>
</div>
<div id="quicksubmit">
<h2>Quick Submit</h2>
Submit by dragging your solution to the dropbox below. Please indicate problem ID and use proper extension. Example: 1895.cpp, P3421.pas
<div id="dropzone"></div>
</div>
{/block}