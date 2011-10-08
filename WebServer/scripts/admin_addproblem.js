var testCases = new Array();

function stringAddOne(s)
{
	
}

function removeCase(i)
{
	$("#testcases").remove("#testcase-"+i);
}

function addCase(input,answer,tl,ml,score)
{
	$("#testcases").append("<tr><td><input type='text' value='"+input+"' /></td><td><input type='text' value='"+answer+"' /></td><td><input type='text' value='"+tl+"' /></td><td><input type='text' value='"+ml+"' /></td><td><input type='text' value='"+score+"' /></td></tr>");
}

function manualAddCase()
{
	addCase($("#man-input").val(),$("#man-answer").val(),$("#man-tl").val(),$("#man-ml").val(),$("#man-score").val());
}

function generateFileList()
{
	beg = parseInt($("#bat-from").val());
	end = parseInt($("#bat-to").val());
	len = parseInt($("#bat-len").val());
	
	inputArr = new Array();
	ansArr = new Array();
	
	for (i=beg;i<=end;i++)
	{
		numStr = i.toString();
		zero = len-numStr.length;
		for (j=0;j<(zero);j++)
		{
			numStr = '0'.concat(numStr);
		}
		inputArr.push($("#bat-input").val().replace('(*)',numStr));
		ansArr.push($("#bat-answer").val().replace('(*)',numStr));
	}
	return {input: inputArr, answer: ansArr};
}

function caseShowPreview()
{
	files = generateFileList();
	$('#bat-examples').empty();
	for (i in files.input)
	{
		$('#bat-examples').append('<li>'+files.input[i]+' '+files.answer[i]+'</li>');
		if (i > 2)
		{
			$('#bat-examples').append('<li>...</li>');
			break;
		}
	}
}

function batchAddCase()
{
	files = generateFileList();
	for (i in files.input)
	{
		addCase(files.input[i],files.answer[i],$("#bat-tl").val(),$("#bat-ml").val(),$("#bat-score").val());
	}
}

function comp(obj)
{
if (obj.selectedIndex == 2){$('#special_judge').show();}else{$('#special_judge').hide();}
}

function toggleScreen(obj,box)
{
	if (obj.checked)
	{
		$(box).hide().value("/SCREEN/");
	}else
	{
		$(box).show().value("");
	}
}