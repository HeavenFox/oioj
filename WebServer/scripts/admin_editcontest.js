function addProblem()
{
	$('#add-problem-indicator').show();
	pid = parseInt($('#add-problem').val());
	jQuery.getJSON('index.php?mod=admin_contest&act=getproblemtitle&id='+pid,function(data){
		$('#add-problem-indicator').hide();
		if (data.error){alert(data.error);}
		else
		{
			addProblemToList(data.title, pid);
		}
	});
	
}

function addProblemToList(title, pid)
{
	$('#problems-list').append("<li>" + title + "<input type='hidden' name='problems[]' value='"+pid+"' />"+removeListItemHTML()+"</li>");
}

function addCriterion()
{
	addCriterionToList($('#add-criterion').val(), document.getElementById('add-criterion-order').selectedIndex == 0 ? 'asc' : 'desc');
	$('#add-criterion').val("");
}

function addCriterionToList(val, order)
{
	$('#criteria-list').append(
	"<li><input name='criteria[]' value='"+val+"' /><select name='criteria-order[]'>"+
		"<option value='a'"+(order == 'asc' ? ' selected' : '')+">ascending</option>"+
		"<option value='d'"+(order == 'desc' ? ' selected' : '')+">descending</option>"+
		"</select>" + removeListItemHTML() + "</li>"
	);
}

function removeListItemHTML()
{
	return '<input type="button" value="x" onclick="$(this).parent().remove()"/>';
}