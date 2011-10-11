{extends file="two-column.tpl"}
{block name="column-left"}
<h2>{$problem->title}</h2>
<div id='problem-body'>
{$problem->body}
</div>
{/block}
{block name="column-right"}
{/block}