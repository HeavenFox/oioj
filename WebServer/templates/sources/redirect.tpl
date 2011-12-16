<!DOCTYPE html> 
<html>
<title>{block name="pagetitle"}OIOJ{/block}</title>
<link rel="stylesheet" href="templates/redirect.css" />

<body>
<div id="box">
<h1>Redirecting...</h1>
<p>{$message}</p>
<p id="link"><a href="{$redirect}">Click here to continue...</a></p>
</div>
</body>
</html>