{extends file="problem.tpl"}
{block name="extra_header" prepend}
<script type="text/javascript" src="scripts/contestproblem.js">
</script>
<script type="text/javascript">
var curContestID = {$c->id};
var curProblemID = {$problem->id};
var endTime = {$user_deadline};
$(document).ready(function(){
	
	updateTimer();
});
</script>
{/block}
{block name="column-right"}
<div>
	<h2>Timer</h2>
	<p id="timer-display"></p>
	<small>Please refrain from using back button. Doing so may disable the timer.</small>
</div>
{$smarty.block.parent}
<div>
	<h2>Contest Info</h2>
	<a><a href="index.php?mod=contest&id={$c->id}">Back to Contest Home Page</a></a>
</div>
{/block}