{extends file="two-column.tpl"}
{block name="html_head" append}
<script type="text/javascript" src="scripts/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="scripts/jquery.fileupload.js"></script>
<script type="text/javascript" src="scripts/submitsoln/submitsoln.js"></script>
<script type="text/javascript" src="scripts/popup_record.js"></script>
{block name="submit_script"}
<script type="text/javascript" src="scripts/submitsoln/submitsoln_prob.js"></script>
<script type="text/javascript">
$(function(){
	$("#problem-submit-box").submitsoln_prob({
		drop: $('#dropzone')
	});
});
</script>
{/block}
{/block}
{block name="column-left"}
<h2>{$problem->title}</h2>
<div id='problem-body'>
{$problem->body}
</div>
{/block}
{block name="column-right"}
<div class="sidebar-box">
<h2>Problem Info</h2>
<ul>
	<li>Submitter: {$problem->user->username}</li>
	<li>Submission: {$problem->submission}</li>
	<li>Accepted: {$problem->accepted}</li>
	{if $problem->source}<li>Source: {$problem->source}</li>{/if}
</ul>
</div>
{block name="problem_submit_box"}
<div class="sidebar-box" id="problem-submit-box">
	<h2>Submit</h2>
	<div id="dropzone">
	</div>
	<div id="manual-upload">
	<form method="post" enctype="multipart/form-data" action="index.php?mod=submit">
		<input type='hidden' name='id' value='{$problem->id}' />
		<input type="file" name="source" />
		<input type="submit" />
	</form>
	</div>
</div>
{/block}
{/block}
