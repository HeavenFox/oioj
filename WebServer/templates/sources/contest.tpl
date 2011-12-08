{extends file="two-column.tpl"}
{block name="column-left"}
<h2>{$c->title}</h2>
<div id="description">{$c->description}</div>
{if $c->problems}
<h3>Problems</h3>
{if $registered && !$started}
<p>Note: By viewing any of the problems below, you start working on this contest and the timer starts to tick.</p>
{/if}
<table>
<thead>
	<tr>
		<td>Title</td><td>Input</td><td>Output</td>
	</tr>
</thead>
<tbody>
	{foreach $c->problems as $problem}
	<tr>
		<td>{if $c->status != 1}<a href="index.php?mod=contestproblem&cid={$c->id}&id={$problem->id}">{/if}{$problem->title}{if $c->status != 1}</a>{/if}</td><td>{$problem->input}</td><td>{$problem->output}</td>
	</tr>
	{/foreach}
</tbody>
</table>
{/if}
{/block}
{block name="column-right"}
<div id="sidebar-box">
<h2>At a Glance</h2>
<ul>
<li>Reg Begin: {if $c->regBegin > 0}{$c->regBegin|date_format}{else}N/A{/if}</li>
<li>Reg Deadline: {if $c->regDeadline > 0}{$c->regDeadline|date_format}{else}N/A{/if}</li>
<li>Contest Begin: {if $c->beginTime > 0}{$c->beginTime|date_format}{else}N/A{/if}</li>
<li>Contest End: {if $c->endTime > 0}{$c->endTime|date_format}{else}N/A{/if}</li>
<li>Duration: {$c->duration|duration_format}</li>
</ul>
</div>
<div id="sidebar-box">
<h2>Your Status</h2>
{nocache}
{if $registered}
<p>You have registered for this contest</p>
{if $started}
<p>You have started working on your problem at {$started|date_format}. Be sure to submit before time runs out!</p>
{else}
<p>You havn't started working yet. Start now!</p>
{/if}
{else}
<p>You have not registered.</p>
<p>{if $user->id != 0}
<a href="index.php?mod=contest&act=register&id={$c->id}">Register now</a>
{else}
<a href="javascript:;" onclick="globalShowLoginBox();return false;">Log in</a> to register
{/if}</p>
{/if}
{/nocache}
</div>
{/block}