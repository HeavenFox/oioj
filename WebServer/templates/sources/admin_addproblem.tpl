{extends file="base.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/admin_addproblem.css' />
<link rel="stylesheet" href="scripts/jquery-ui-css/ui-lightness/jquery-ui-1.8.16.custom.css" />
<script type='text/javascript' src='scripts/jquery-ui-1.8.16.custom.min.js'></script>
<script type="text/javascript" src="scripts/admin_addproblem.js"></script>
<script type="text/javascript" src="lib/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="lib/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript" src="scripts/jquery.fileupload.js"></script>
<script type="text/javascript" src="scripts/jquery.iframe-transport.js"></script>
<script type='text/javascript'>
function addTags()
{
	var tags = $('#tag_input').val().split(';');
	$.each(tags, function(idx,val){
		addTagRow(0,$.trim(val));
	});
	
}

function addTagRow(id,tag)
{
	$('#tags_added').append(
		$('<li />')
		.text(tag)
		.append(
			$('<input type="hidden" name="tag_tid[]" />')
			.attr('value',id)
		)
		.append(
			$('<input type="hidden" name="tag_tag[]" />')
			.attr('value',tag)
		)
	);
}
$(function(){
	CKEDITOR.replace("sf_problem_body",{
		toolbar: [
			{ name: 'document', items : [ 'Source','Preview' ] },
			{ name: 'basicstyles', items : [ 'Bold','Italic','Subscript','Superscript','-','RemoveFormat' ] },
			{ name: 'paragraph', items : [ 'Format','NumberedList','BulletedList','Image' ] },
			{ name: 'links', items : [ 'Link','Unlink' ] },
			{ name: 'tools', items : [ 'Maximize' ] }
		],
        filebrowserUploadUrl : 'index.php?mod=admin_problem&act=uploadimage'
	});
	
	$('#attachments').fileupload(
	{
		dataType: "json",
		url: "index.php?mod=admin_problem&act=uploadattachments",
		singleFileUploads: false,
		limitConcurrentUploads: 3,
		done: function(e,data)
		{
			$.each(data.result, function(idx,file){
				$('#uploaded_attachments').append(
					$('<li />')
					.text(file.fileName)
					.append(
						$('<input type="hidden" name="attach_storedname[]" />')
						.attr('value',file.storedName)
					)
					.append(
						$('<input type="hidden" name="attach_filename[]" />')
						.attr('value',file.fileName)
					)
				);
			});
		}
	}
	);
	
	$('#tag_input').autocomplete({
		source: 'index.php?mod=problemlist&act=tagcomplete&ajax=1',
		select:function(event, ui){ 
			event.preventDefault();
			addTagRow(ui.item.value,ui.item.label);
			$(this).val('');
		}
	});
});
</script>
{/block}
{block name="body"}
<h2>{if $sf_problem->fresh}Add{else}Edit{/if} Problem</h2>
{sform obj=$sf_problem enctype="multipart/form-data"}
<div id="main">
	<fieldset>
	<legend>Problem</legend>
	<p>{sinput id="title" placeholder='Enter title here...'}</p>
	<p>{sinput id="body"}</p>
	</fieldset>
{if $sf_problem->fresh}
	<fieldset><legend>Test Cases</legend>
	<table id='testcases-table'>
	<thead><tr><td>Input</td><td>Answer</td><td>Time (s)</td><td>Mem (MB)</td><td>Score</td><td></td></tr></thead>
	<tbody id="testcases">
	</tbody>
	<tfoot>
	<tr><td><input type="text" id="man-input" size="12" /></td><td><input type="text" id="man-answer" size="12" /></td><td><input type="text" id="man-tl" size="4" /></td><td><input type="text" id="man-ml" size="4" /></td><td><input type="text" id="man-score" size="4" /></td><td><a href="javascript:;" onclick="manualAddCase();return false;">[+]</a></td></tr>
	</tfoot>
	</table><a id='batch_box_link' href='#batch_box'>Batch Add</a>
	<p>Data Archive<input type="file" name="archive" /><br /><small>Must be a ZIP archive that contains all test cases</small></p>
	</fieldset>
{/if}
	<input type='submit' value='Submit' />
</div>
<div id="aside">
	<fieldset>
		<legend>Basic</legend>
		{sinput id='listing'}{slabel id='listing'}
	</fieldset>
	{if $sf_problem->fresh}
	<fieldset id='attachments'>
		<legend>Attachments</legend>
		<small>To upload an image for use, use editor's "insert image" icon<br />Tip: you can upload multiple files at once</small>
		<input type='file' name='attach[]' multiple="multiple" />
		<ul id='uploaded_attachments'></ul>
	</fieldset>
	<fieldset id='tags'>
		<legend>Tags</legend>
		<small>Use colon (;) to separate multiple tags</small>
		<ul id='tags_added'></ul>
		<input id='tag_input' /><input type='button' value='Add' onclick='addTags()' />
	</fieldset>
	<fieldset>
	<legend>Input, Output</legend>
	<table>
	<tr>
	<td>{slabel id="input_file"}</td>
	    <td>{sinput id="input_file" size="9"}
	    <input type="checkbox" name="screen_input" id="screen_input" onchange="toggleScreen(this,'#sf_problem_input_file')" />
	    <label for="screen_input">Screen</label></td>
	</tr>
	<tr>
	<td>{slabel id="output_file"}</td>
	<td>
	    {sinput id="output_file" size="9"}
	    <input type="checkbox" name="screen_output" id="screen_output" onchange="toggleScreen(this,'#sf_problem_output_file')" />
	    <label for="screen_output">Screen</label></td>
	</tr>
	<tr>
	    <td><label for="type">{slabel id="type"}</label></td>
	    <td>
	    {sinput id='type'}
	    </td>
	 </tr>
	 <tr>
		<td>{slabel id="comp_method"}</td>
	    <td>{sinput id='comp_method'}
	    <div id="special_judge" class="hidden"><label for="special_judge_input">bin name</label>
	    <input type="text" name="special_judge" id="special_judge_input" /></div></td>
	</tr>
	</table>
	</fieldset>
	{/if}
</div>
{/sform}
<div class="hidden">
<div id="batch_box"><h3>Batch Add Test Cases</h3><p>Use (*) to represent serial number</p>
<p>Input: <input id="bat-input" value="data(*).in" onkeyup='caseShowPreview()' /> Answer: <input id="bat-answer" value="data(*).out" onkeyup='caseShowPreview()' /> Time Limit: <input id="bat-tl" value="1" /> Memory Limit: <input id="bat-ml" value="128" /> Score: <input id="bat-score" value="10" /></p>
<p>From: <input id="bat-from" value="1" size="6" maxlength="3" onkeyup='caseShowPreview()' /> To: <input id="bat-to" value="10" size="6" maxlength="3" onkeyup='caseShowPreview()' /> Length: <input id="bat-len" value="2" size="3" maxlength="2" onkeyup='caseShowPreview()' /></p><p><input type='button' value='Batch Add' onclick='batchAddCase()' /></p>
<p>Examples</p><ul id='bat-examples'></ul></div></div>
{/block}