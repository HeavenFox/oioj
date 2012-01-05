{extends file="two-column.tpl"}
{block name="column-left"}
<h2>Add Contest</h2>
<div id="main">
<h3>Contest Basics</h3>
<div id="sec-1">
<table>
<tr><td>Title</td><td><input type="text" name="title" /></td></tr>
<tr><td>Description</td><td><textarea name="description"></textarea></td></tr>
<tr><td>Scheduled Start Time</td><td><small>Note: This is for information only. Unless directed, contest will not automatically start at this time.</small></td></tr>
<tr><td>Scheduled End Time</td><td></td></tr>
<tr><td>Duration</td><td></td></tr>
</table>
</div>
<h3>Registration</h3>
<div>
<table>
<tr><td>Automatic Registration</td><td></td></tr>
<tr><td>Allow Registration</td><td></td></tr>

<tr><td>Registration Begins</td><td></td></tr>
<tr><td>Registration Ends</td><td></td></tr>

<tr><td>Current Registrants</td><td></td></tr>
</table>
</div>
<h3>Judging & Ranking</h3>
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

<h3>Problems</h3>
<div>
<table>
<tr><td>Display titles before contest starts</td><td><input type="checkbox" name="display-title-before-start" /></td></tr>
</table>
</div>
<h3>Automation</h3>
<div>
<table>
<tr><td>Automatically start at scheduled time</td><td></td></tr>
<tr><td>Automatically send to judge servers</td><td></td></tr>
<tr><td>Automatically bring judge servers exclusive</td><td><small>If checked, these servers will no longer accept normal requests. Recommended for live feedback scenario.</small></td></tr>
</table>
</div>
</div>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}