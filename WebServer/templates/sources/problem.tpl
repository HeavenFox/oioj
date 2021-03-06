{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/problem.css' />
<link rel="stylesheet" href="scripts/jquery-ui-css/ui-lightness/jquery-ui-1.8.16.custom.css" />
<script type="text/javascript" src="scripts/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="scripts/loader.js"></script>
<script type="text/javascript" src="scripts/jquery.fileupload.js"></script>
<script type="text/javascript" src="scripts/submitsoln/submitsoln.js"></script>
<script type="text/javascript" src="scripts/popup_record.js"></script>
{block name="submit_script"}
<script type="text/javascript" src="scripts/submitsoln/submitsoln_prob.js"></script>
<script type="text/javascript">
var curProblemID = {$problem->id};
</script>
<script type="text/javascript">
$(function(){
	$("#problem-submit-box").submitsoln_prob({
		drop: $('#dropzone'),
		inClass: 'in',
		hoverClass: 'hover',
		uploadStatus: $('#submit_status')
	});
});
</script>
{ifable to="edit_tags"}
<script type="text/javascript" src="scripts/tagmanage.js"></script>
{endif}

{/block}
<script type="text/javascript" src="scripts/mathjax/MathJax.js?config=default"></script>
{/block}
{block name="column-left"}
<div id='title_bar'>
<div class="titles">
<h1>{$problem->title}</h1>
</div>
{block name="titlebar_links"}
<div class="links">Problem&nbsp;&nbsp;<a href='index.php?mod=problem&act=discussion&id={$problem->id}'>Discussion</a>&nbsp;&nbsp;<a href=''>Solution</a></div>
{/block}
</div>
{block name="problem_body"}
<div id='problem-body'>
{$problem->body}
</div>
{/block}
{/block}
{block name="column-right"}
<div class="sidebar-box">
<h2>Problem Info</h2>
<div class="sidebar-content">
<ul>
	<li>ID: {$problem->id}</li>
	{if $problem->user->username}<li>Submitter: {$problem->user->username|escape}</li>{/if}
	<li>Input: {if $problem->input == "/SCREEN/"}Screen{else}{$problem->input|escape}{/if}</li>
	<li>Output: {if $problem->output == "/SCREEN/"}Screen{else}{$problem->output|escape}{/if}</li>
	<li>Submission: {$problem->submission}</li>
	<li>Accepted: {$problem->accepted}</li>
	{if $problem->source}<li>Source: {$problem->source|escape}</li>{/if}
</ul>
</div>
</div>
{block name="tags_box"}
<div class="sidebar-box">
<h2><a href='javascript:;'>Tags</a></h2>
<div class="sidebar-content">
<div id='taglist'>
{if $problem->tags}
{foreach $problem->tags as $tag}
<span class="tag">{$tag->tag|escape}
{ifable to="edit_tags"}
<a href="javascript:;" onclick="removeTag({$problem->id},{$tag->id},this);">[x]</a>
{endif}
</span>
{/foreach}
{/if}
</div>
{ifable to="edit_tags"}
<div>
<input id='tag_input' size='5' /><input type='button' value='+' onclick='addTagFromInput()' />
</div>
{endif}
</div>
</div>
{/block}
{if $problem->attachments}
<div class="sidebar-box">
<h2>Attachments</h2>
<div class="sidebar-content">
<ul>
{foreach $problem->attachments as $a}
<li><a href="index.php?mod=problem&act=attach&aid={$a->id}">{$a->filename|escape}</a></li>
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
		<input type="hidden" name="MAX_FILE_SIZE" value="10240" />
		<input type="file" name="source" />
		<input type="submit" value='Submit' /><a href='index.php?mod=submit&amp;id={$problem->id}'>Use Form Instead</a>
	</form>
	<div id="submit_status" class="hidden" style='text-align: center'></div>
	<div id="dropzone">
	</div>
	</div>
	</div>
</div>
{/block}
{/block}
