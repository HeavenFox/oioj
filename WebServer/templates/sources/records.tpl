{extends file="base.tpl"}
{block name="html_head" append}
<script type="text/javascript" src="scripts/records.js"></script>
<link rel='stylesheet' href='templates/list.css' />
<link rel="stylesheet" href="templates/records.css" />
{/block}
{block name="body"}
<table id='records' class='tablist'>
<thead><tr><td style="width: 25px;">ID</td><td>Problem</td><td style="width: 80px;">Server</td><td style="width: 100px;">Status</td><td style="width: 40px;">Score</td><td style="width: 20px;">Language</td><td style="width: 100px;">Author</td><td style="width: 170px;">Time</td></tr></thead>
<tbody>
{foreach $records as $record}
<tr class='{cycle values="odd,even"}'>
  <td>{$record->id}</td>
  <td><a href="index.php?mod=problem&amp;id={$record->problem->id}">{$record->problem->title}</a></td>
  <td>{$record->server->name}</td>
  <td class='{$record->statusClass}'><a href='javascript:showCaseInfo({$record->id});'>{$record->status}</a></td>
  <td>{$record->score}</td>
  <td>{$record->lang}</td>
  <td><a href="user.php?uid={$record->user->id}">{$record->user->username}</a></td>
  <td>{$record->timestamp|datetime_format}</td>
</tr>
<tr class="caseinfo" id="caseinfo-{$record->id}"><td colspan="8">
<ul>
{foreach $record->cases as $list}
<li>{$list}</li>
{/foreach}
</ul>
</td></tr>
{/foreach}
</tbody>
</table>
{/block}
