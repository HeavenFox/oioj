<!DOCTYPE html> 
<html>
<title>{block name="pagetitle"}OIOJ{/block}</title>
<link rel="stylesheet" href="templates/redirect.css" />

{block name="extra_header"}
{/block}

<body>
<div id="box">
<p>{$message}</p>
<p><a href="{$redirect}">Click here to continue...</a></p>
</div>
</body>
</html>