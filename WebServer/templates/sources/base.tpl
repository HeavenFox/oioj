<!DOCTYPE html> 
<html>
<head>
{block name="html_head"}
<title>{block name="pagetitle"}OIOJ{/block}</title>
<meta charset="utf-8">
<link rel="stylesheet" href="templates/reset.css" />
<link rel="stylesheet" href="templates/base.css" />

<script type="text/javascript" src="scripts/jquery.min.js"></script>
<script type="text/javascript" src="scripts/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="scripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="scripts/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript" src="scripts/base.js"></script>
{/block}
</head>

<body>
<div id="header">
<div id="logo"><a href="index.php"><img src='templates/images/header.png' alt='OIOJ Instructional Online Judge' /></a></div>
<div id="navbar"><ul><li><a href="index.php?mod=learning">Learning Center</a></li><li><a href="index.php?mod=problemlist">Problems</a></li><li><a href="index.php?mod=records">Records</a></li><li><a href="index.php?mod=contestlist">Arena</a></li></ul></div>
</div>
{if isset($global_message)}<div id="global_message">{$global_message}<div id='global_message_close'><a href='#' onclick="$('#global_message').addClass('hidden');return false;">x</a></div></div>{/if}
<div id="container"><div id="infobar"><div id="breadcrumb"><a href='index.php'>Home</a>
{if isset($breadcrumb)}
{foreach $breadcrumb as $k => $v}
 » {if $v}<a href='{$v|escape}'>{/if}{$k}{if $v}</a>{/if}
{/foreach}
{/if}
</div><div id="userinfo">Welcome, {$user->username}
{if $user->id != 0}
{ifable to="admin_cp"}<a href='index.php?mod=admin_home'>Admin CP</a> - {endif} <a href='index.php?mod=user&amp;act=editprofile'>User Center</a> - <a href='index.php?mod=user&amp;act=logout'>Log out</a>
{else}
 <a href='javascript:;' onclick='globalShowLoginBox()'>Log in</a>
{/if}
</div></div>

<div id="body">{block name="body"}<!-- DEFAULT CONTENT -->{/block}</div>
<div id="footer">Page Generated at: {$smarty.now|date_format:"%Y-%m-%d %T, Server Time Zone %z"}<br />OIOJ Instructional Online Judge (c){$smarty.now|date_format:"%Y"} Zhu Jingsi. All Rights Reserved.</div></div>

</body>
</html>