<!DOCTYPE html> 
<html>
<title>{block name="pagetitle"}OIOJ{/block}</title>
<script type="text/javascript" src="scripts/jquery.min.js"></script>
<link rel="stylesheet" href="templates/reset.css" />
<link rel="stylesheet" href="templates/base.css" />

{block name="extra_header"}
{/block}

<body>
<div id="header">
<div id="logo"><a href="index.php"><img src='templates/images/header.png' /></a></div>
<div id="navbar"><ul><li><a href="index.php?mod=learning">Learning Center</a></li><li><a href="index.php?mod=problemlist">Problems</a></li><li><a href="index.php?mod=records">Records</a></li><li><a href="index.php?mod=contestlist">Arena</a></li></ul></div>
</div>
<div id="container"><div id="infobar"><div id="breadcrumb">Home</div><div id="userinfo">Welcome, {$user->username}
{if $user->id != 0}
 <a href='index.php?mod=user&act=editprofile'>User Center</a> - <a href='index.php?mod=user&act=logout'>Log out</a>
{/if}
</div></div>

<div id="body">{block name="body"}<!-- DEFAULT CONTENT -->{/block}</div>
<div id="footer">OIOJ Instructional Online Judge (c){$smarty.now|date_format:"%Y"} Zhu Jingsi. All Rights Reserved.</div></div>
</body>
</html>