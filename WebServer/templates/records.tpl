{extends file="base.tpl"}
{block name="body"}
<table id='records'>
<thead><tr><td>ID</td><td>Status</td><td>Score</td><td>Author</td><td>Time</td></tr></thead>
<tbody>
{foreach $records as $record}
<tr class='{cycle values="odd,even"}'>
  <td>{$record.id}</td>
  
  <td>{$record.result}</td>
  <td>{$record.score}</td>
  <td><a href="user.php?uid={$record.authorid}">{$record.author}</a></td>
  <td>2011-1-1 10:11:01</td>
</tr>
<tr class="caseinfo" id="caseinfo-{$record.id}"><td colspan="5">Case 1: Accepted. Time: 0.02s Memory: 1.223MB</td></tr>
{/foreach}
</tbody>
</table>
{/block}