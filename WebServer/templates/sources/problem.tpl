{extends file="two-column.tpl"}
{block name="extra_header" prepend}
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
{/block}
{block name="column-left"}
<h2>{$problem->title}</h2>
<div id='problem-body'>
{$problem->body}
</div>
{/block}
{block name="column-right"}
<div>
<ul>
	<li>Submission:</li>
	<li>Accepted:</li>
	<li>Source:</li>
</ul>
</div>
<div>
	<h2>Submit</h2>
	<div id="dropzone">
	</div>
	<div id="manual-upload">
	<form enctype="multipart/form-data" action="index.php?mod=submit&id={$problem->id}">
		<input type="file" name="source" />
		<input type="submit" />
	</form>
	</div>
</div>
{/block}