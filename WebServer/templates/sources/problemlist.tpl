{extends file="base.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
{/block}
{block name="body"}
<table id='problems'>
<thead><tr><td width="20px">ID</td><td>Title</td><td width="100px">Acceptance</td></tr></thead>
<tbody>
{foreach $problems as $problem}
<tr class="{cycle values="odd,even"}">
<td>{$problem->id}</td><td><a href="index.php?mod=problem&id={$problem->id}">{$problem->title}</a></td><td>{$problem->accepted}/{$problem->submission}
{if $problem->submission > 0}
 ({($problem->accepted/$problem->submission*100)|string_format:"%.1f"}%)
{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
{/block}