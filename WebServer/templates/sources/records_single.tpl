{extends file="base.tpl"}
{block name="html_head" append}
{if !$results_available}
<script type='text/javascript'>
function setRefresh(time)
{
	if (time == 0)
	{
		window.location = window.location;
	}else
	{
		$('#indication').text('This page will refresh in '+time+' seconds');
		setTimeout('setRefresh('+(time-1)+')',1000);
	}
}

$(function()
{
	setRefresh(5);
});
</script>
{/if}
{/block}
{block name="body"}
{include "boxes/records_single.tpl"}
<div>
{if !$results_available}
<p id='indication'>This page will refresh in 5 seconds</p>
<p>Or you may hit refresh button on your browser</p>
{/if}
</div>
{/block}