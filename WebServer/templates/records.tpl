{extends file="base.tpl"}
{block name="body"}
<table id='records'>
<thead><tr><td>ID</td><td>Status</td><td>Score</td><td>Author</td><td>Time</td></tr></thead>
<tbody>
{foreach $records as $record}
<tr class='{cycle values="odd,even"}'>
  <td>1</td>
  
  <td>Accepted</td>
  <td>40</td>
  <td>HeavenFox</td>
  <td>2011-1-1 10:11:01</td>
</tr>
{/foreach}
</tbody>
</table>
{/block}