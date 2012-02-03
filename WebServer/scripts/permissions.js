var prompted = false;
function editPerm(val,query,obj)
{
	$(obj).text('...');
	$.post("index.php?mod=admin_user&act=doeditperm&ajax=1",query,function(data){
		if (data.error == undefined)
		{
			$(obj).text(val);
		}else
		{
			alert(data.error);
		}
		setColor();
		calculateSum();
	},'json');
}
function editTagPerm(obj,check)
{
	if (prompted || !check || confirm("Note: You are editing permission for a tag, which will potentially affect many users. Are you sure?\nTo edit this user only, use User-Specific permission."))
	{
		var input = prompt("Enter new value");
		if (input !== null)
		{
			editPerm(parseInt(input),{
				tid: $(obj).data("tid"),
				key: $(obj).data("key"),
				old: $(obj).text(),
				"new": parseInt(input)
			},obj);
		}
	}
}

function editUserPerm(obj)
{
	var input = prompt("Enter new value");
	if (input !== null)
	{
		editPerm(parseInt(input),{
			uid: $(obj).data("uid"),
			key: $(obj).data("key"),
			old: $(obj).text(),
			"new": parseInt(input)
		},obj);
	}
}

function calculateSum()
{
	$('.perm_sum_num').each(function(){
		var sum = 0;
		$(this).nextAll('.perm_num').each(function(){
			sum += parseInt($(this).text());
		});
		$(this).text(sum);
	});
}

function setColor()
{
	$('.perm_num, .perm_num a').each(function()
	{
		var val = parseInt($(this).text());
		if (val > 0)
		{
			$(this).css('color','green');
		}
		else if (val < 0)
		{
			$(this).css('color','red');
		}
		else
		{
			$(this).css('color','#3f3f3f');
		}
	});
}