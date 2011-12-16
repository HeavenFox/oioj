{extends file="two-column.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
<link rel="stylesheet" href="templates/contestlist-home.css" />
{/block}
{block name="column-left"}
<h2>Open Contests</h2>
<table class="tablist">
<thead>
<tr>
  <td>ID</td><td>Name</td><td>Starter</td><td>Reg Deadline</td><td>Begin</td><td>End</td><td>Duration</td>
</tr>
</thead>
<tbody>
{if $open_contests}
{foreach $open_contests as $c}
<tr>
  <td>{$c->id}</td><td><a href="index.php?mod=contest&id={$c->id}">{$c->title}</a></td><td>{$c->user->username}</td><td>{if $c->regDeadline > 0}{$c->regDeadline|datetime_format}{else}N/A{/if}</td><td>{if $c->beginTime > 0}{$c->beginTime|datetime_format}{else}N/A{/if}</td><td>{if $c->endTime > 0}{$c->endTime|datetime_format}{else}N/A{/if}</td><td>{$c->duration|duration_format}</td>
</tr>
{/foreach}
{else}
<tr>
<td class="none" colspan="7">
  None
</td>
</tr>
{/if}
</tbody>
</table>
<h2>Contest In Progress</h2>
<table class="tablist">
<thead>
<tr>
  <td>ID</td><td>Name</td><td>Starter</td><td>Begin</td><td>End</td><td>Duration</td>
</tr>
</thead>
<tbody>
{if $inprogress_contests}
{foreach $inprogress_contests as $c}
<tr>
  <td>{$c->id}</td><td><a href="index.php?mod=contest&id={$c->id}">{$c->title}</a></td><td>{$c->user->username}</td><td>{if $c->beginTime > 0}{$c->beginTime|datetime_format}{else}N/A{/if}</td><td>{if $c->beginTime > 0}{$c->endTime|datetime_format}{else}N/A{/if}</td><td>{$c->duration|duration_format}</td>
</tr>
{/foreach}
{else}
<tr>
<td class="none" colspan="6">
  None
</td>
</tr>
{/if}
</tbody>
</table>
<h2>Ready Contests</h2>
<table class="tablist">
<thead>
<tr>
  <td>ID</td><td>Name</td><td>Starter</td><td>Begin</td><td>End</td><td>Duration</td>
</tr>
</thead>
<tbody>
{if $ready_contests}
{foreach $ready_contests as $c}
<tr>
  <td>{$c->id}</td><td><a href="index.php?mod=contest&id={$c->id}">{$c->title}</a></td><td>{$c->user->username}</td><td>{if $c->beginTime > 0}{$c->beginTime|datetime_format}{else}N/A{/if}</td><td>{if $c->beginTime > 0}{$c->endTime|datetime_format}{else}N/A{/if}</td><td>{$c->duration|duration_format}</td>
</tr>
{/foreach}
{else}
<tr>
<td class="none" colspan="6">
  None
</td>
</tr>
{/if}
</tbody>
</table>
<h2>Future Contests</h2>
<table class="tablist">
<thead>
<tr>
  <td>ID</td><td>Name</td><td>Starter</td><td>Reg Begin</td><td>Reg Deadline</td><td>Begin</td><td>End</td><td>Duration</td>
</tr>
</thead>
<tbody>
{if $future_contests}
{foreach $future_contests as $c}
<tr>
  <td>{$c->id}</td><td><a href="index.php?mod=contest&id={$c->id}">{$c->title}</a></td><td>{$c->user->username}</td><td>{if $c->regBegin > 0}{$c->regBegin|datetime_format}{else}N/A{/if}</td><td>{if $c->regDeadline > 0}{$c->regDeadline|datetime_format}{else}N/A{/if}</td><td>{if $c->beginTime > 0}{$c->beginTime|datetime_format}{else}N/A{/if}</td><td>{if $c->beginTime > 0}{$c->endTime|datetime_format}{else}N/A{/if}</td><td>{$c->duration|duration_format}</td>
</tr>
{/foreach}
{else}
<tr>
<td class="none" colspan="8">
  None
</td>
</tr>
{/if}
</tbody>
</table>
<h2>Past Contests</h2>
<table class="tablist">
<thead>
<tr>
  <td>ID</td><td>Name</td><td>Starter</td><td>Begin</td><td>End</td><td>Duration</td>
</tr>
</thead>
<tbody>
{if $past_contests}
{foreach $past_contests as $c}
<tr>
  <td>{$c->id}</td><td><a href="index.php?mod=contest&id={$c->id}">{$c->title}</a></td><td>{$c->user->username}</td><td>{if $c->beginTime > 0}{$c->beginTime|datetime_format}{else}N/A{/if}</td><td>{if $c->beginTime > 0}{$c->endTime|datetime_format}{else}N/A{/if}</td><td>{$c->duration|duration_format}</td>
</tr>
{/foreach}
{else}
<tr>
<td class="none" colspan="6">
  None
</td>
</tr>
{/if}
</tbody>
</table>
{/block}
{block name="column-right"}
{/block}