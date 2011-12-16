{extends file="base.tpl"}
{block name="html_head" append}
<script type="text/javascript" src="scripts/records.js"></script>
<link rel='stylesheet' href='templates/list.css' />
<link rel="stylesheet" href="templates/records.css" />
{/block}
{block name="body"}
<table id='records' class='tablist'>
<thead><tr><td width="20px">ID</td><td>Problem</td><td width="40px">Status</td><td width="40px">Score</td><td width="20px">Language</td><td width="100px">Author</td><td width="180px">Time</td></tr></thead>
<tbody>
{foreach $records as $record}
<tr class='{cycle values="odd,even"}'>
  <td>{$record->id}</td>
  <td>{$record->problem->title}</td>
  <td class='{$record->statusClass}'><a href='javascript:showCaseInfo({$record->id});'>{$record->status}</a></td>
  <td>{$record->score}</td>
  <td>{$record->lang}</td>
  <td><a href="user.php?uid={$record->user->id}">{$record->user->username}</a></td>
  <td>{$record->timestamp|datetime_format}</td>
</tr>
<tr class="caseinfo" id="caseinfo-{$record->id}"><td colspan="7">
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
