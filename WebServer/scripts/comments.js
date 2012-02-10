function commentFlipPage(page)
{
	loader.show();
	$.get('index.php?mod=problem&act=comments&page='+page+'&id='+curProblemID,function(data)
	{
		$('#comments').empty().append($(data).parseSpoiler());
		loader.hide();
		curPage = page;
	},'html');
	
}
function submitComment(obj)
{
	$(obj).find("input, textarea").attr('disabled','disabled');
	loader.show();
	var comment = obj.content.value;
	var parent = parseInt(obj.parent.value);
	$.post('index.php?mod=problem&act=discussion&do=post&id='+curProblemID,{ 'content': comment, 'parent':parent },function(data)
	{
		loader.hide();
		newComment = $(data);
		if (parent > 0)
		{
			var nextObj = $('#comment-'+parent).next();
			if (nextObj.hasClass('threaded_comment'))
			{
				nextObj.append(newComment);
			}
			else
			{
				$('<div class="threaded_comment"></div>').append(newComment).insertAfter($('#comment-'+parent));
			}
			
		}else
		{
			$('#comments_list').append(newComment);
		}
		newComment.css('opacity',0.1).animate({ 'opacity': 1 },500);
		$(window).scrollTop(newComment.position().top-10);
		
		$(obj).find("input, textarea").removeAttr('disabled');
		$(obj).find("textarea").val('');
		clearReply();
		
		newComment.parseSpoiler();
	},'html');
}

function clearReply()
{
	$('#postcomment input[name="parent"]').val(0);
	$('#reply_indicator').hide();
}

function replyComment(id)
{
	$('#postcomment input[name="parent"]').val(id);
	$('#postcomment textarea').focus();
	$('#reply_indicator').show();
}