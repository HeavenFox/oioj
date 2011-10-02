<!DOCTYPE html> 
<html>
<title>{block name="pagetitle"}OIOJ{/block}</title>
<script type="text/javascript" src="scripts/jquery.min.js"></script>
<link rel="stylesheet" href="templates/reset.css" />
<link rel="stylesheet" href="templates/base.css" />

{block name="extra_header"}
{/block}
</html>
<body>
<div id="header">
<div id="logo"><img src='templates/images/header.png' /></div>
<div id="navbar"><ul><li>Learning Center</li><li>Problems</li><li>Records</li><li>Arena</li></ul></div>
</div>
<div id="infobar"><div id="breadcrumb">Home</div><div id="userinfo">Welcome, Profile - Log out</div></div>
<div id="container"><div id="body">{block name="body"}<!-- DEFAULT CONTENT -->{/block}</div>
<div id="footer">OIOJ Instructional Online Judge (c)2011 Zhu Jingsi. All Rights Reserved.</div></div>
</body>