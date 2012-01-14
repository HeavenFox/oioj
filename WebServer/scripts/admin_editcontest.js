function addProblem()
{
	$('#add-problem-indicator').show();
	pid = parseInt($('#add-problem').val());
	jQuery.getJSON('index.php?mod=admin_contest&act=getproblemtitle&id='+pid,function(data){
		$('#add-problem-indicator').hide();
		if (data.error){alert(data.error);}
		else
		{
			$('#problems-list').append("<li>" + data.title + "<input type='hidden' name='problems[]' value='"+pid+"' /></li>");
		}
	});
	
}

function addCriterion()
{
	$('#criteria-list').append(
	"<li><input name='criteria[]' value='"+$('#add-criterion').val()+"' /><select name='criteria-order'>"+
		"<option value='a'"+(document.getElementById('add-criterion-order').selectedIndex == 0 ? ' selected' : '')+">ascending</option>"+
		"<option value='d'"+(document.getElementById('add-criterion-order').selectedIndex == 1 ? ' selected' : '')+">descending</option>"+
		"</select></li>"
	);
	$('#add-criterion').val("");
}