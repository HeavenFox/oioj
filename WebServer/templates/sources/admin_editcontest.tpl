{extends file="two-column.tpl"}
{block name="column-left"}
<h2>Add Problem</h2>
<div id="">
<ul>
<li>Contest Basics</li>
<li>Registration</li>
<li>Ranking</li>
<li>Problems</li>
<li>Options</li>
</ul>
</div>
<div id="">
<div>
Title
Description
Scheduled Start Time
Scheduled End Time
Duration
</div>
<div>
Automatic Registration
Allow Registration

Registration Begins
Registration Ends

Current Registrants
</div>
<div>
Display Ranking
... Before Judge Finishes
Ranking Criteria
Add

</div>
<div>
<table>
<tr>
<td>
Display titles before contest starts
</td>
<td>
<input type="checkbox" name="display-title-before" />
</td>
Automatically start at scheduled time
Automatically send to judge server
</div>
</div>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}