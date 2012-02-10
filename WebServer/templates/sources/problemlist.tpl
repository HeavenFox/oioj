{extends file="base.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
<link rel='stylesheet' href='templates/pager.css' />
<link rel='stylesheet' href='templates/tagquery.css' />
<link rel='stylesheet' href='templates/problemlist.css' />
<link rel="stylesheet" href="scripts/jquery-ui-css/ui-lightness/jquery-ui-1.8.16.custom.css" />
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
	//$('#tag_search').autocomplete({ source: function(req, callback){
	//	$.post('index.php?mod=problemlist&act=tagcomplete&ajax=1',req,callback,'json');
	//} });
	$('#tag_search').autocomplete({ source: 'index.php?mod=problemlist&act=tagcomplete&ajax=1', select:function(event, ui){ 
		event.preventDefault();
		$('<span class="tag" data-tid="'+ui.item.value+'"><a href="index.php?mod=problemlist&tag='+ui.item.value+'">'+ui.item.label+'</a></span>').draggable({ revert: 'invalid' }).appendTo($('#popular_tags_list'));
		$(this).val('');
		
	} });
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

#popular_tags
{
	height: 30px;
}

#popular_tags_list
{
	float: left;
	height: 30px;
	line-height: 30px;
}

#popular_tags_search
{
	float: right;
	line-height: 30px;
}

</style>
{/block}
{block name="body"}
<div id='title_bar'>
<div class="titles"><h1>Problems</h1>
</div>
	<div class="links"><a href='javascript:;' onclick='$("#tags").toggle(500);$(this).toggleClass("on");'>Tags</a>&nbsp;&nbsp;<a href='javascript:;' onclick='$("#search").toggle(500);$(this).toggleClass("on");'>Search</a></div>
</div>
<div id='search' class="hidden"><form method="post" action="index.php?mod=problemlist&amp;act=search">Search by Title <input type="text" name="keyword" /><input type="submit" value="Search" /></form></div>
<div id="tags" class="hidden">
	<div id='popular_tags'>
		<div id='popular_tags_list'>
		Popular Tags: 
		{foreach $tags as $tag}
		<span class='tag' data-tid='{$tag->id}'><a href='index.php?mod=problemlist&amp;tag={$tag->id}'>{$tag->tag|escape}</a></span>
		{/foreach}
		</div>
		<div id='popular_tags_search'>
			<label for='tag_search'>Search Tag</label> <input id='tag_search' size='5' /><a href='javascript:;' onclick='$("#tag_query").toggle(1000)'>Advanced</a>
		</div>
	</div>
	<div id='tag_query' class='hidden'>
		<div class='intersect_group ig_new'><ul></ul></div>
		<div id='tagquery_right'>
			<div id='trash'></div>
			<input type='button' onclick='tagQuerySubmit();' value='Submit' />
		</div>
	</div>
</div>
<table id='problems' class='tablist'>
<thead><tr><td style="width: 50px;">ID</td><td>Title</td><td style="width: 130px;">Acceptance</td></tr></thead>
<tbody>
{foreach $problems as $problem}
<tr class="{cycle values="odd,even"}">
<td>{$problem->id}</td><td><a href="index.php?mod=problem&amp;id={$problem->id}">{$problem->title|escape}</a> {if $problem->listing == 0}(Hidden){/if} {if $problem->dispatched == 0}(To be dispatched){/if}</td><td>{$problem->accepted}/{$problem->submission}
{if $problem->submission > 0}
 ({($problem->accepted/$problem->submission*100)|string_format:"%.1f"}%)
{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
{pager cur=$page_cur max=$page_max url="index.php?mod=problemlist&page=%d" form="index.php?mod=problemlist" var="page"}
{ifable to="add_problem"}<a href="index.php?mod=admin_problem&act=add">Add New Problem</a>{endif}
{/block}