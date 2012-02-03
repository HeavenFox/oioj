{extends file="base.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/two-column.css' />
<script type='text/javascript'>
$(function(){
	$('#column-right .sidebar-box h2 a').bind("click",function(event){
		$(this).parent().next().toggle(500);
		$(this).toggleClass('collapsed');
	});
});

</script>
{/block}
{block name="body"}
<div id="column-left">
{block name="column-left"}{/block}
</div>
<div id="column-right">
{block name="column-right"}{/block}
</div>
<div style='clear:both'></div>
{/block}