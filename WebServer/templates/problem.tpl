{extends file="two-column.tpl"}
{block name="column-left"}
<h2>{$problem->title}</h2>
<div id='problem-body'>
{$problem->body}
</div>
{/block}
{block name="column-right"}
<div class="sidebar-box">
<h2>At a Glance</h2>
<ul>
<li>Input: </li>
<li>Output: </li>
	<li>Author: </li>
	<li>Submission: </li>
	<li>Accepted: </li>
	<li>Rating: </li>
	<li>Source:</li>
</ul>
</div>
<div>
<h2>Rate this Problem</h2>
</div>
<div>
<h2>Submit Solution</h2>
</div>
{/block}