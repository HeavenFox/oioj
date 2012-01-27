{extends file="base.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/list.css' />
<link rel='stylesheet' href='templates/pager.css' />

<script type='text/javascript' src='scripts/jquery-ui-1.8.16.custom.min.js'></script>
<script type='text/javascript'>
(function($){
	$.fn.intersectGroup = function()
	{
		this.droppable({
			drop: function(event, ui)
			{
				if ($(this).hasClass('ig_new'))
				{
					$(this).after('<div class="union"></div><div class="intersect_group ig_new"></div>');
					$(this).nextAll().css({ 'marginLeft' : 50, 'opacity': 0.0 }).animate({ 'marginLeft' : 5, 'opacity': 1 },1000);
					$(this).next().next().intersectGroup();
					$(this).removeClass('ig_new');
				}
			}
		});
	};
})(jQuery);
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
</script>
<style type='text/css'>
#tag_query
{
	height: 200px;
}
.tag
{
	padding: 5px;
	background-color: blue;
	width:30px;
}
.intersect_group
{
	width: 150px;
	height: 170px;
	background: url('templates/images/tagging/intersec.png') #eaeaea no-repeat center center;
	border: 1px solid #949494;
	float: left;
	
	margin: 10px;
	transition: width 1s, height 1s;
	-moz-transition: width 1s, height 1s;
	-o-transition: width 1s, height 1s;
	-webkit-transition: width 1s, height 1s;
}

.ig_new
{
	width: 100px;
	height: 150px;
}

.union
{
	width: 50px;
	height: 100px;
	background-color: green;
	float: left;
}

#trash
{
	width: 95px;
	height: 95px;
	border: 1px solid #949494;
	background: url('templates/images/tagging/trashcan.png') no-repeat top left;
	float: right;
}

#trash.hover
{
	background-position: 0 -95px;
}
</style>
{/block}
{block name="body"}
<div id='tag_query'>
<div class='intersect_group ig_new'></div>
<div id='trash'></div>
</div>
<div>Popular Tags: 
{foreach $tags as $tag}
<div class='tag' data-tid='{$tag->id}'>{$tag->tag}</div>
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