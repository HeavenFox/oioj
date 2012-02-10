function loadComments()
{
	loader.show();
	$.get('index.php?mod=problem&act=comments&id=',function(data){
		loader.hide();
	},'html');
}