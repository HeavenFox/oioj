{extends file="base.tpl"}
{block name="body"}
<table id='records'>
<thead><tr><td>ID</td><td>Problem</td><td>Status</td><td>Score</td><td>Language</td><td>Author</td><td>Time</td></tr></thead>
<tbody>
{foreach $records as $record}
<tr class='{cycle values="odd,even"}'>
  <td>{$record.id}</td>
  <td>{$record.prob_title}</td>
  <td class='{$record.status_class}'><a href='javascript:showCaseInfo({$record.id});'>{$record.status}</a></td>
  <td>{$record.score}</td>
  <td>{$record.lang}</td>
  <td><a href="user.php?uid={$record.uid}">{$record.username}</a></td>
  <td>{$record.timestamp|date_format}</td>
</tr>
<tr class="caseinfo" id="caseinfo-{$record.id}"><td colspan="7">
<ul>
{foreach $record.cases as $list}
<li>{$list}</li>
{/foreach}
</ul>
</td></tr>
{/foreach}
</tbody>
</table>
{/block}
{block name="extra_header"}
<script type="text/javascript" src="scripts/records.js"></script>
<link rel="stylesheet" href="templates/records.css" />
{/block}