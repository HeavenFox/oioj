{extends file="two-column.tpl"}
{block name="html_head" append}
<script type="text/javascript" src="scripts/admin_addproblem.js"></script>
<script type="text/javascript" src="lib/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="lib/ckeditor/adapters/jquery.js"></script>

<script type='text/javascript'>
$(document).ready(function(){
	CKEDITOR.replace("prob_body",{
		toolbar: [
			{ name: 'document', items : [ 'Preview' ] },
			{ name: 'basicstyles', items : [ 'Bold','Italic','Subscript','Superscript','-','RemoveFormat' ] },
			{ name: 'paragraph', items : [ 'NumberedList','BulletedList' ] },
			{ name: 'links', items : [ 'Link','Unlink' ] },
			{ name: 'tools', items : [ 'Maximize' ] }
		]
	});
});
</script>
{/block}
{block name="column-left"}
<form method="post" action="index.php?mod=admin_problem&act=add&submit=1" enctype="multipart/form-data" target="console" id="add_form">
<table>
<tr><td><label for="title">Title</label></td><td>
  <input type="text" name="title" id="title" /></td></tr>
<tr><td><label for="input">Body</label></td><td>
  <textarea name="body" id="prob_body"></textarea></td></tr>
<tr><td><label for="input_file">Input</label></td><td>
    <input type="text" name="input_file" id="input_file" />
    <input type="checkbox" name="screen_input" id="screen_input" onchange="toggleScreen(this,'#input_file')" />
    <label for="screen_input">Screen</label></td></tr>
<tr><td><label for="output_file">Output</label></td><td>
    <input type="text" name="output_file" id="output_file" />
    <input type="checkbox" name="screen_output" id="screen_output" onchange="toggleScreen(this,'#output_file')" />
    <label for="screen_output">Screen</label></td></tr>
<tr><td><label for="type">Type</label></td><td>
    <select name="type" id="type">
      <option value="1" selected="selected">Traditional</option>
      <option value="2">Interactive</option>
      <option value="3">Output</option>
    </select></td></tr>
<tr><td><label for="comp_method">Compare Method</label></td><td>
    <select name="comp_method" id="comp_method" onchange="compareBox(this)">
      <option value="/FULLTEXT/" selected="selected">Full Text</option>
      <option value="/OMITSPACE/">Omit Spaces at Line Ends</option>
      <option value="special">Special Judge</option>
    </select>
    <div id="special_judge" class="hidden"><label for="special_judge">bin name</label>
    <input type="text" name="special_judge" id="special_judge" /></div></td></tr>
<tr><td>Test Cases</td><td>
<table>
<thead><tr><td>Input</td><td>Answer</td><td>Time (s)</td><td>Mem (MB)</td><td>Score</td><td></td></tr></thead>
<tbody id="testcases">
<tr></tr>
</tbody>
<tfoot>
<tr><td><input type="text" id="man-input" size="8" /></td><td><input type="text" id="man-answer" size="8" /></td><td><input type="text" id="man-tl" size="4" /></td><td><input type="text" id="man-ml" size="4" /></td><td><input type="text" id="man-score" size="4" /></td><td><a href="javascript:;" onclick="manualAddCase();return false;">[+]</a></td></tr>
</tfoot>
</table>
<a id='batch_box_link' href='#batch_box'>Batch Add</a>
</td></tr>
<tr><td>Data Archive</td><td><input type="file" name="archive" /></td></tr>
<tr><td colspan="2"><input type='submit' /></td></tr>
</table>
</form>
<iframe src="about:blank" id="console" name="console"></iframe>
<div class="hidden">
<div id="batch_box"><h3>Batch Add Test Cases</h3><p>Use (*) to represent serial number</p>
<p>Input: <input id="bat-input" value="data(*).in" onkeyup='caseShowPreview()' /> Answer: <input id="bat-answer" value="data(*).out" onkeyup='caseShowPreview()' /> Time Limit: <input id="bat-tl" value="1" /> Memory Limit: <input id="bat-ml" value="128" /> Score: <input id="bat-score" value="10" /></p>
<p>From: <input id="bat-from" value="1" size="6" maxlength="3" onkeyup='caseShowPreview()' /> To: <input id="bat-to" value="10" size="6" maxlength="3" onkeyup='caseShowPreview()' /> Length: <input id="bat-len" value="2" size="3" maxlength="2" onkeyup='caseShowPreview()' /></p><p><input type='button' value='Batch Add' onclick='batchAddCase()' /></p>
<p>Examples</p><ul id='bat-examples'></ul></div></div>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}