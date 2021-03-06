{extends file="base.tpl"}
{block name="html_head" append}
<script type="text/javascript" src="scripts/records.js"></script>
<link rel='stylesheet' href='templates/list.css' />
<link rel="stylesheet" href="templates/records.css" />
<link rel="stylesheet" href="templates/pager.css" />
{/block}
{block name="body"}
<table id='records' class='tablist'>
<thead><tr><td style="width: 25px;">ID</td><td>Problem</td><td style="width: 80px;">Server</td><td style="width: 100px;">Status</td><td style="width: 40px;">Score</td><td style="width: 20px;">Language</td><td style="width: 100px;">Author</td><td style="width: 170px;">Time</td></tr></thead>
<tbody>
{foreach $records as $record}
<tr class='{cycle values="odd,even"}'>
  <td>{$record->id}</td>
  <td><a href="index.php?mod=problem&amp;id={$record->problem->id}">{$record->problem->title|escape}</a></td>
  <td>{$record->server->name|escape}</td>
  <td class='{$record->statusClass}'>{if $record->status > JudgeRecord::STATUS_DISPATCHED}<a href='javascript:showCaseInfo({$record->id});'>{/if}{$record->statusString}{if $record->status > JudgeRecord::STATUS_DISPATCHED}</a>{/if}</td>
  <td>{$record->score}</td>
  <td>{$record->lang|escape}</td>
  <td><a href="user.php?uid={$record->user->id}">{$record->user->username|escape}</a></td>
  <td>{$record->timestamp|datetime_format}</td>
</tr>
<tr class="caseinfo" id="caseinfo-{$record->id}"><td colspan="8">
<ul>
{foreach $record->cases as $list}
<li>{$list|escape}</li>
{/foreach}
</ul>
</td></tr>
{/foreach}
</tbody>
</table>
{pager cur=$page_cur max=$page_max url="index.php?mod=records&page=%d" form="index.php?mod=records" var="page"}
{/block}
