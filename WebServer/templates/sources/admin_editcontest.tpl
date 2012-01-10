{extends file="two-column.tpl"}
{block name="html_head" append}
<script type="text/javascript" src="scripts/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="scripts/admin_editcontest.js"></script>
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
<form action="index.php?mod=admin_contest&act=save" method="post">
<div id="prefs">
<h3><a href='#'>Contest Basics</a></h3>
<div id="sec-1">
<table>
<tr><td>Title</td><td><input type="text" name="title" /></td></tr>
<tr><td>Description</td><td><textarea name="description"></textarea></td></tr>
<tr><td>Scheduled Start Time</td><td><input type="date" name="starttime-date" onchange="p=document.getElementById('starttime-date');if (!p.value)p.value=this.value;" /><input type="number" name="starttime-h" min="0" max="23" step="1" value="0" />:<input type="number" name="starttime-m" min="0" max="59" step="1" value="0" />:<input type="number" name="starttime-s" min="0" max="59" step="1" value="0" /><br /><small>Note: This is for information only. Unless directed, contest will not automatically start at this time.</small></td></tr>
<tr><td>Scheduled End Time</td><td><input type="date" name="endtime-date" id="endtime-date" /><input type="number" name="endtime-h" min="0" max="23" step="1" value="0" />:<input type="number" name="endtime-m" min="0" max="59" step="1" value="0" />:<input type="number" name="endtime-s" min="0" max="59" step="1" value="0" /></td></tr>
<tr><td>Duration</td><td><input type="number" name="duration-h" min="0" />h <input type="number" name="duration-m" min="0" max="59" step="1" />min <input type="number" name="duration-s" min="0" max="59" step="1" />sec<br /><small>This does not have to match end minus start. User can begin anytime during that window and have this much time to finish.</small></td></tr>
<tr><td>Support Early Hand-in</td><td><input type="checkbox" name="early_handin" /><small>User will be able to stop working anytime, after which submission is disabled. Useful for ranking by time spent.</td></tr>
<tr><td>Automatically start at scheduled time</td><td><input type="checkbox" name="auto_start" /></td></tr>

</table>
</div>
<h3><a href='#'>Registration & Problems</a></h3>
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

<tr><td>Registration Begins</td><td>
<input type="date" name="regstart-date" /><input type="number" name="regstart-h" min="0" max="23" step="1" value="0" />:<input type="number" name="regstart-m" min="0" max="59" step="1" value="0" />:<input type="number" name="regstart-s" min="0" max="59" step="1" value="0" />
</td></tr>
<tr><td>Registration Ends</td><td>
<input type="date" name="regend-date" /><input type="number" name="regend-h" min="0" max="23" step="1" value="0" />:<input type="number" name="regend-m" min="0" max="59" step="1" value="0" />:<input type="number" name="regend-s" min="0" max="59" step="1" value="0" />
</td></tr>

<tr><td>Display titles before contest starts</td><td><input type="checkbox" name="display_problem_title_before_start" /></td></tr>
<tr><td>Problems</td><td><ul id="problems-list"></ul><input type="number" name="add-problem" id="add-problem" /><input type="button" onclick="addProblem();" value='Add' /><span id="add-problem-indicator" style='display: none;'>Fetching problem info...</span><br /><small>Please put the ID of problems here. Add problems first if you haven't</small></td></tr>

</table>
</div>
<h3><a href='#'>Judging & Ranking</a></h3>
<div>
<table>
<tr><td>After Submission</td><td>
<select name="after_submit">
<option value="save">Save</option>
<option value="judge">Judge</option>
</select>
</td></tr>
<tr><td>Automatically send to judge servers</td><td><input type="checkbox" name="auto_judge" /> <input type='number' name='judge-hiatus' value='10' /> minutes after contest ends<br /><small>You may need some time to deal with unexpected situations.</small></td></tr>
<tr><td>Display Ranking</td><td><input type="checkbox" name="display_ranking" /></td></tr>
<tr><td>... Before Judge Finishes</td><td><input type="checkbox" name="display_preliminary_ranking" /></td></tr>
<tr><td>Ranking Criteria</td><td><ul id="criteria-list"><li></li></ul><input id="add-criterion" /><select id="add-criterion-order"><option value="a">ascending</option><option value="a">descending</option></select><input type='button' value='Add' onclick='addCriterion()' /><p>You can use any PHP expression as criteria. The following parameters are available:</p>
<p><ul><li>num_right: Number of correct submissions</li>
<li>num_wrong: Number of wrong submissions</li>
<li>num_right: Number of correct submissions</li>
<li>duration: Time user used (in sec) before handing in. You have to enable "early hand-in" to use this.</li>
<li>total_time: Sum of time elapsed, in sec, before each correct submission (ACM style)</li>
<li>max_time: Time elapsed, in sec, before last correct submission.</li>
<li>total_score: Total score.</li></ul></p>
<p>Sample: Standard ACM ranking<br />Criterion 1: num_right, descending<br />Criterion 2: 20*60*num_wrong+duration, ascending</p>
</td></tr>
<tr><td>Ranking Parameters to Display</td><td>
<select name="display_params[]" multiple="multiple">
<option value="total_score">Total Score</option>
<option value="num_right">Num of Correct Submission</option>
<option value="num_wrong">Num of Wrong Submission</option>
<option value="duration">Time Used Before Hand-in</option>
<option value="total_time">Sum of Elapsed Time When Submitted</option>
<option value="max_time">Elapsed Time When Last Submitted</option>
</select>
<br /><small>Note: the parameters you select MUST appear in ranking criteria calculation</small>
</td></tr>
</table>
</div>

</div>
<input type="submit" value="Save" />
</form>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}