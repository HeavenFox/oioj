{extends file="two-column.tpl"}
{block name="html_head" append}
<script type="text/javascript" src="scripts/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" href="scripts/jquery-ui-css/ui-lightness/jquery-ui-1.8.16.custom.css" />
<script type='text/javascript'>
$(function(){
	$('#prefs').accordion();
	$('input[type="date"]').datepicker();
});
</script>
{/block}
{block name="column-left"}
<h2>Add Contest</h2>
<div id="prefs">
<h3><a href='#'>Contest Basics</a></h3>
<div id="sec-1">
<table>
<tr><td>Title</td><td><input type="text" name="title" /></td></tr>
<tr><td>Description</td><td><textarea name="description"></textarea></td></tr>
<tr><td>Scheduled Start Time</td><td><input type="date" name="starttime" onchange="p=document.getElementById('endtime');if (!p.value)p.value=this.value;" /><small>Note: This is for information only. Unless directed, contest will not automatically start at this time.</small></td></tr>
<tr><td>Scheduled End Time</td><td><input type="date" name="endtime" id="endtime" /></td></tr>
<tr><td>Duration</td><td><input type="number" name="duration-h" min="0" />h <input type="number" name="duration-m" min="0" max="59" step="1" />min <input type="number" name="duration-h" min="0" max="59" step="1" />sec<br /><small>This does not have to match end minus start. User can begin anytime during that window and have this much time to finish.</small></td></tr>
</table>
</div>
<h3><a href='#'>Registration</a></h3>
<div>
<table>
<tr><td>Publicity Level</td><td>
<select>
<option value="0">Unlisted: contest will be invisible to ordinary user</option>
<option value="1">Internal: contest is visible, but not available for register</option>
<option value="2">Register: users need to register beforehand</option>
<option value="3">Auto: automatically register user once begin working</option>
</select>
</td></tr>

<tr><td>Registration Begins</td><td></td></tr>
<tr><td>Registration Ends</td><td></td></tr>

</table>
</div>
<h3><a href='#'>Judging & Ranking</a></h3>
<div>
<table>
<tr><td>After Submission</td><td>
<select>
<option>Save</option>
<option>Judge</option>
</select>
</td></tr>
<tr><td>Judge Server</td><td>
<select multiple>
<option>Any</option>
<option>Judge</option>
</select>
</td></tr>

<tr><td>Display Ranking</td><td></td></tr>
<tr><td>... Before Judge Finishes</td><td></td></tr>
<tr><td>Ranking Criteria</td><td></td></tr>
</table>
</div>

<h3><a href='#'>Problems</a></h3>
<div>
<table>
<tr><td>Display titles before contest starts</td><td><input type="checkbox" name="display-title-before-start" /></td></tr>
<tr><td>Problems</td><td><input name="problems" /><br /><small>Please put the IDs of problems here, separated by a comma. Please add problems first if you havn't</small></td></tr>
</table>
</div>
<h3><a href='#'>Automation</a></h3>
<div>
<table>
<tr><td>Automatically start at scheduled time</td><td><input type="checkbox" name="auto-start" /></td></tr>
<tr><td>Automatically send to judge servers</td><td><input type="checkbox" name="auto-judge" /></td></tr>
<tr><td>Automatically bring judge servers exclusive</td><td><input type="checkbox" name="auto-shut-judgeserver" /><small>If checked, these servers will no longer accept normal requests. Recommended for live feedback scenario.</small></td></tr>
</table>
</div>
</div>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}