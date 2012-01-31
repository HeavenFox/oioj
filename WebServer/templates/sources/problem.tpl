{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/problem.css' />
<script type="text/javascript" src="scripts/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="scripts/jquery.fileupload.js"></script>
<script type="text/javascript" src="scripts/submitsoln/submitsoln.js"></script>
<script type="text/javascript" src="scripts/popup_record.js"></script>
{block name="submit_script"}
<script type="text/javascript" src="scripts/submitsoln/submitsoln_prob.js"></script>
<script type="text/javascript">
$(function(){
	$("#problem-submit-box").submitsoln_prob({
		drop: $('#dropzone'),
		inClass: 'in',
		hoverClass: 'hover'
	});
});
</script>
{/block}
<script type="text/javascript" src="scripts/mathjax/MathJax.js?config=default"></script>
{/block}
{block name="column-left"}
<div id='title_bar'>
<h2>{$problem->title}</h2>
<div>Problem&nbsp;&nbsp;<a href=''>Discussion</a>&nbsp;&nbsp;<a href=''>Solution</a></div>
</div>
<div id='problem-body'>
{$problem->body}
</div>
{/block}
{block name="column-right"}
<div class="sidebar-box">
<h2>Problem Info</h2>
<div class="sidebar-content">
<ul>
	<li>ID: {$problem->id}</li>
	{if $problem->user->username}<li>Submitter: {$problem->user->username}</li>{/if}
	<li>Input: {if $problem->input == "/SCREEN/"}Screen{else}{$problem->input}{/if}</li>
	<li>Output: {if $problem->output == "/SCREEN/"}Screen{else}{$problem->output}{/if}</li>
	<li>Submission: {$problem->submission}</li>
	<li>Accepted: {$problem->accepted}</li>
	{if $problem->source}<li>Source: {$problem->source}</li>{/if}
</ul>
</div>
</div>
{if $problem->attachments}
<div class="sidebar-box">
<h2>Attachments</h2>
<div class="sidebar-content">
<ul>
{foreach $problem->attachments as $a}
<li><a href="index.php?mod=problem&act=attach&aid={$a->id}">{$a->filename}</a></li>
{/foreach}
</ul>
</div>
</div>
{/if}
{block name="problem_submit_box"}
<div class="sidebar-box" id="problem-submit-box">
	<h2>Submit</h2>
	<div class="sidebar-content">
	<div id="manual-upload">
	<form method="post" enctype="multipart/form-data" action="index.php?mod=submit&amp;solution=1">
		<input type='hidden' name='id' value='{$problem->id}' />
		<input type="file" name="source" />
		<input type="submit" /><a href='index.php?mod=submit&amp;id={$problem->id}'>Use Form Instead</a>
	</form>
	</div>
	<div id="dropzone">
	</div>
	</div>
</div>
{/block}
{/block}
