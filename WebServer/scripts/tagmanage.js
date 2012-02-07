function removeTag(pid,tid,obj)
{
	$.get('index.php?mod=admin_problem&act=removetag&tid='+tid+'&pid='+pid,function(data)
	{
		$(obj).parent().remove();
	});
}
function addTag(pid,tid,tag)
{
	$.post('index.php?mod=admin_problem&act=addtag',{ 'tid': tid, 'pid': pid, 'tag': tag },function(data)
	{
		$('#taglist').append($('<span class="tag">'+tag+'<a href="javascript:;" onclick="removeTag('+pid+','+data.tid+',this);">[x]</a></span>'));
	},'json');
}
function addTagFromInput()
{
	addTag(curProblemID,0,$('#tag_input').val());
	$('#tag_input').val('').focus();
}
$(function(){
	$('#tag_input').autocomplete({
		source: 'index.php?mod=problemlist&act=tagcomplete&ajax=1',
		select: function(event, ui){ 
			event.preventDefault();
			addTag(curProblemID,ui.item.value,ui.item.label);
			$(this).val('').focus();
		}
	}).keydown(function(event)
	{
		if (event.keyCode == '13') {
			addTagFromInput();
		}
	});
});