{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
<link rel='stylesheet' href='templates/contest.css' />
{/block}
{block name="column-left"}
<div id="title_bar">
<h1>{$c->title}</h1>
</div>
<div id="description">{$c->description}</div>
{if $c->problems}
<h2>Problems</h2>
{if $registered && !$started}
<p>Note: By viewing any of the problems below, you start working on this contest and the timer starts to tick.</p>
{/if}
<table class='tablist'>
<thead>
	<tr>
		<td>Title</td><td>Input</td><td>Output</td>
	</tr>
</thead>
<tbody>
	{foreach $c->problems as $problem}
	<tr>
		<td>{if $c->status > Contest::STATUS_WAITING}<a href="index.php?mod=contestproblem&amp;cid={$c->id}&amp;id={$problem->id}">{/if}{$problem->title|escape}{if $c->status > Contest::STATUS_WAITING}</a>{/if}</td><td>{$problem->input|escape}</td><td>{$problem->output|escape}</td>
	</tr>
	{/foreach}
</tbody>
</table>
{/if}
{if isset($ranking)}
<h2>Ranking</h2>
{include file="boxes/contest_ranking.tpl"}
{/if}
{/block}
{block name="column-right"}
<div class="sidebar-box">
<h2>At a Glance</h2>
<div class="sidebar-content">
<ul>
<li>Status:
{if $c->status == Contest::STATUS_WAITING}Waiting{/if}
{if $c->status == Contest::STATUS_INPROGRESS}In Progress{/if}
{if $c->status == Contest::STATUS_FINISHED}Finished{/if}
{if $c->status == Contest::STATUS_JUDGING}Judging{/if}
{if $c->status == Contest::STATUS_JUDGED}Judged{/if}
</li>
<li>Registration Begin: {if $c->regBegin > 0}<br />{$c->regBegin|datetime_format}{else}N/A{/if}</li>
<li>Registration Deadline: {if $c->regDeadline > 0}<br />{$c->regDeadline|datetime_format}{else}N/A{/if}</li>
<li>Contest Begin: {if $c->beginTime > 0}<br />{$c->beginTime|datetime_format}{else}N/A{/if}</li>
<li>Contest End: {if $c->endTime > 0}<br />{$c->endTime|datetime_format}{else}N/A{/if}</li>
<li>Duration: {$c->duration|duration_format}</li>
</ul>
</div>
</div>
{nocache}
<div class="sidebar-box">
<h2>Your Status</h2>
<div class="sidebar-content">
{if $registered}
	<p>You have registered for this contest</p>
	{if $started}
		<p>You have started working on your problem at {$started|datetime_format}. Be sure to submit before time runs out!</p>
	{else}
		<p>You havn't started working yet. Start now!</p>
	{/if}
{else}
	<p>You have not registered.</p>
	<p>{if $current_user->id != 0}
	<a href="index.php?mod=contest&amp;act=register&amp;id={$c->id}">Register now</a>
	{else}
	<a href="javascript:;" onclick="globalShowLoginBox();return false;">Log in</a> to register
	{/if}</p>
{/if}
</div>
</div>
{if $current_user->ableTo('contestcp') || $current_user->id == $c->user->id}
<div class="sidebar-box">
<h2>Manage</h2>
<div class="sidebar-content">
	<a href="index.php?mod=contestcp&id={$c->id}">Control Panel</a>
</div>
</div>
{/if}
{/nocache}
{/block}