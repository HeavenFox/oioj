{extends file="base.tpl"}
{block name="html_head" append}
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
	setRefresh(4);
});
</script>
{/block}
{block name="body"}
{include "boxes/records_single.tpl"}
<div>
<p id='indication'>This page will refresh in 4 seconds</p>
<p>Or you may hit refresh button on your browser</p>
</div>
{/block}