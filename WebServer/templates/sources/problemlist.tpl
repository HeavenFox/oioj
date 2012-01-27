{extends file="base.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
<link rel='stylesheet' href='templates/pager.css' />
<link rel='stylesheet' href='templates/tagquery.css' />
<script type='text/javascript' src='scripts/jquery-ui-1.8.16.custom.min.js'></script>
<script type='text/javascript' src='scripts/tagquery.js'></script>
<script type='text/javascript'>
$(function(){
	$('.tag').draggable({
		revert: 'invalid'
	});
	$('.intersect_group').intersectGroup();
	$('#trash').droppable(
	{
		drop: function(event, ui)
		{
			ui.helper.remove();
		},
		hoverClass: 'hover'
	}
	);
});
function tagQuerySubmit()
{
	window.location="index.php?mod=problemlist&tagquery="+escape(JSON.stringify($('#tag_query').intersectGroupData()));
}
</script>
<style type='text/css'>
.tag
{
	font-size: 12px;
	padding: 3px;
	background-color: #dbdbdb;
	border: 1px solid #c2c2c2;
	border-radius: 3px;
	width:30px;
}

</style>
{/block}
{block name="body"}
<div id='tag_query'>
<div class='intersect_group ig_new'><ul></ul></div>
<div id='tagquery_right'>
<div id='trash'></div>
<input type='button' onclick='tagQuerySubmit();' value='Submit' />
</div>
</div>
<div id='popular_tags'>Popular Tags: 
{foreach $tags as $tag}
<span class='tag' data-tid='{$tag->id}'><a href='index.php?mod=problemlist&amp;tag={$tag->id}'>{$tag->tag}</a></span>
{/foreach}
</div>
<table id='problems' class='tablist'>
<thead><tr><td style="width: 50px;">ID</td><td>Title</td><td style="width: 100px;">Acceptance</td></tr></thead>
<tbody>
{foreach $problems as $problem}
<tr class="{cycle values="odd,even"}">
<td>{$problem->id}</td><td><a href="index.php?mod=problem&amp;id={$problem->id}">{$problem->title}</a></td><td>{$problem->accepted}/{$problem->submission}
{if $problem->submission > 0}
 ({($problem->accepted/$problem->submission*100)|string_format:"%.1f"}%)
{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
{pager cur=$page_cur max=$page_max url="index.php?mod=problemlist&page=%d" form="index.php?mod=problemlist" var="page"}
{/block}