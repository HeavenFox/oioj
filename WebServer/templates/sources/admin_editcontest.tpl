{extends file="two-column.tpl"}
{block name="html_head" append}
<script type="text/javascript" src="scripts/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="scripts/admin_editcontest.js"></script>
<link rel="stylesheet" href="scripts/jquery-ui-css/ui-lightness/jquery-ui-1.8.16.custom.css" />
<link rel="stylesheet" href="templates/admin_editcontest.css" />
<script type='text/javascript'>
$(function(){
	$.datepicker.setDefaults({
		"dateFormat":"yy-mm-dd"
	});
	$('input[type="date"]').datepicker();
});
</script>
{/block}
{block name="column-left"}
<div id='title_bar'>
<div class="titles">
<h1>Add Contest</h1>
</div>
</div>
{sform obj=$contestform}
<table>
<thead>
<tr><td colspan="2">Contest Basics</td></tr>
</thead>
<tbody>
<tr><td>{slabel id="title"}</td><td>{sinput id="title"}</td></tr>
<tr><td>{slabel id="description"}</td><td>{sinput id="description"}</td></tr>
<tr><td>{slabel id="starttime"}</td><td>{sinput id="starttime" onchange="p=document.getElementById('sf_contest_starttime');if (!p.value)p.value=this.value;"}<br /><small>Note: This is for information only. Unless directed, contest will not automatically start at this time.</small></td></tr>
<tr><td>{slabel id="endtime"}</td><td>{sinput id="endtime"}</td></tr>
<tr><td>{slabel id="duration"}</td><td>{sinput id="duration"}<br /><small>This does not have to match end minus start. User can begin anytime during that window and have this much time to finish.</small></td></tr>
<tr><td>{slabel id="early_handin"}</td><td>{sinput id="early_handin"}<small>User will be able to stop working anytime, after which submission is disabled. Useful for ranking by time spent.</td></tr>
<tr><td>{slabel id="auto_start"}</td><td>{sinput id="auto_start"}</td></tr>
</tbody>
<thead>
<tr><td colspan="2">Registration &amp; Problems</td></tr>
</thead>
<tbody>
<tr><td>{slabel id="publicity"}</td><td>
{sinput id="publicity"}
</td></tr>

<tr><td>{slabel id="regstart"}</td><td>
{sinput id="regstart"}
</td></tr>
<tr><td>{slabel id="regend"}</td><td>
{sinput id="regend"}
</td></tr>

<tr><td>{slabel id="display_problem_title_before_start"}</td><td>{sinput id="display_problem_title_before_start"}</td></tr>
<tr><td>Problems</td><td><ul id="problems-list"></ul><input type="number" name="add-problem" id="add-problem" /><input type="button" onclick="addProblem();" value='Add' /><span id="add-problem-indicator" style='display: none;'>Fetching problem info...</span><br /><small>Please put the ID of problems here. Add problems first if you haven't</small></td></tr>
</tbody>
<thead>
<tr><td colspan="2">Judging &amp; Ranking</td></tr>
</thead>
<tbody>
<tr><td>{slabel id="after_submit"}</td><td>
{sinput id="after_submit"}
</td></tr>
<tr><td>Automatically send to judge servers</td><td>{sinput id="auto_judge"} {sinput id='judge_hiatus'} minutes after contest ends<br /><small>You may need some time to deal with unexpected situations.</small></td></tr>
<tr><td>Display Ranking</td><td>{sinput id="display_ranking"}... Before Judge Finishes{sinput id="display_preliminary_ranking"}</td></tr>
<tr><td>Ranking Criteria</td><td><ul id="criteria-list"></ul><input id="add-criterion" /><select id="add-criterion-order"><option value="a">ascending</option><option value="a">descending</option></select><input type='button' value='Add' onclick='addCriterion()' /><p>You can use any PHP expression as criteria. The following parameters are available:</p>
<p><ul><li>num_right: Number of correct submissions</li>
<li>num_wrong: Number of wrong submissions</li>
<li>num_right: Number of correct submissions</li>
<li>duration: Time user used (in sec) before handing in. You have to enable "early hand-in" to use this.</li>
<li>total_time: Sum of time elapsed, in sec, before each correct submission (ACM style)</li>
<li>max_time: Time elapsed, in sec, before last correct submission.</li>
<li>total_score: Total score.</li></ul></p>
<p>Sample: Standard ACM ranking<br />Criterion 1: num_right, descending<br />Criterion 2: 20*60*num_wrong+duration, ascending</p>
</td></tr>
<tr><td>{slabel id="display_params"}</td><td>
{sinput id="display_params"}
<br /><small>Note: the parameters you select MUST appear in ranking criteria calculation</small>
</td></tr>
</tbody>
</table>
<input type="submit" value="Save" />
{/sform}
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}