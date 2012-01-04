{extends file="problem.tpl"}
{block name="html_head" append}
<script type="text/javascript" src="scripts/contestproblem.js"></script>
{nocache}
<script type="text/javascript">
var curContestID = {$c->id};
var curProblemID = {$problem->id};
var endTime = {$user_deadline};
$(document).ready(function(){
	updateTimer();
});
</script>
{/nocache}
{/block}

{block name="submit_script"}
<script type="text/javascript" src="scripts/submitsoln/submitsoln_contest.js"></script>
<script type="text/javascript">
$(function(){
	$("#problem-submit-box").submitsoln_contest({
		drop: $('#dropzone')
	});
});
</script>
{/block}

{block name="column-right"}
<div class="sidebar-box">
	<h2>Timer</h2>
	<p id="timer-display"></p>
	<small></small>
</div>
{$smarty.block.parent}
<div class="sidebar-box">
	<h2>Contest Info</h2>
	<a><a href="index.php?mod=contest&id={$c->id}">Back to Contest Home Page</a></a>
</div>
{/block}
{block name="problem_submit_box"}
<div class="sidebar-box" id="problem-submit-box">
	<h2>Submit</h2>
	<div id="dropzone">
	</div>
	<div id="manual-upload">
	<form method="post" enctype="multipart/form-data" action="index.php?mod=contestproblem&amp;act=submit">
		<input type="file" name="source" />
<input type='hidden' name='id' value='{$problem->id}' />
<input type='hidden' name='cid' value='{$c->id}' />
		<input type="submit" />
	</form>
	</div>
</div>
{/block}