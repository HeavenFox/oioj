{extends file="base.tpl"}
{block name="extra_header"}
<link rel='stylesheet' href='templates/problemlist.css' />
{/block}
{block name="body"}
<table id='problems'>
<thead><tr><td>ID</td><td>Title</td><td>Sub</td><td>AC</td></tr></thead>
<tbody>
{foreach $problems as $problem}
<tr><td>{$problem->id}</td><td>{$problem->title}</td><td>{$problem->submission}</td><td>{$problem->accepted}</td></tr>
{/foreach}
</tbody>
</table>
{/block}