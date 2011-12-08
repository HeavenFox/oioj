{extends file="base.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/two-column.css' />
{/block}
{block name="body"}
<div id="column-left">
{block name="column-left"}{/block}
</div>
<div id="column-right">
{block name="column-right"}{/block}
</div>
{/block}