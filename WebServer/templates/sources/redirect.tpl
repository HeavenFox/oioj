<!DOCTYPE html> 
<html>
<title>{block name="pagetitle"}OIOJ{/block}</title>
<link rel="stylesheet" href="templates/redirect.css" />
<script type="text/javascript">
setTimeout(function(){
	window.location = "{$redirect|escape:'quotes'}";
},3000);
</script>
<body>
<div id="box">
<h1>Redirecting...</h1>
<div id="message">
<p>{$message|escape}</p>
<p id="link"><a href="{$redirect|escape}">Click here if you're not automatically redirected...</a></p>
</div>
</div>
</body>
</html>