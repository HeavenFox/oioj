{extends file="two-column.tpl"}
{block name="extra_header" prepend}
<script type="text/javascript" src="scripts/admin_addproblem.js"></script>
{/block}
{block name="column-left"}
<form method="post" action="index.php?mod=admin_problem&act=add&submit=1" enctype="multipart/form-data">
<table>
<tr><td><label for="title">Title</label></td><td>
  <input type="text" name="title" id="title" /></td></tr>
<tr><td><label for="input">Body</label></td><td>
  <textarea name="body" id="body"></textarea></td></tr>
<tr><td><label for="input_file">Input</label></td><td>
    <input type="text" name="input_file" id="input_file" />
    <input type="checkbox" name="screen_input" id="screen_input" onchange="toggleScreen(this,'#input_file')" />
    <label for="screen_input">Screen</label></td></tr>
<tr><td><label for="output_file">Output</label></td><td>
    <input type="text" name="output_file" id="output_file" />
    <input type="checkbox" name="screen_output" id="screen_output" onchange="toggleScreen(this,'#output_file')" />
    <label for="screen_output">Screen</label></td></tr>
<tr><td><label for="comp_method">Compare Method</label></td><td>
    <select name="comp_method" id="comp_method" onchange="comp(this)">
      <option value="/FULLTEXT/" selected="selected">Full Text</option>
      <option value="/OMITSPACE/">Omit Spaces at Line Ends</option>
      <option value="special">Special Judge</option>
    </select>
    <div id="special_judge"><label for="special_judge">bin name</label>
    <input type="text" name="special_judge" id="special_judge" /></div></td></tr>
<tr><td>Test Cases</td><td>
<table>
<thead><tr><td>Input</td><td>Answer</td><td>Time Limit (s)</td><td>Memory Limit (MB)</td><td>Score</td><td></td></tr></thead>
<tbody id="testcases">
<tr></tr>
</tbody>
<tfoot>
<tr><td><input type="text" id="man-input" /></td><td><input type="text" id="man-answer" /></td><td><input type="text" id="man-tl" /></td><td><input type="text" id="man-ml" /></td><td><input type="text" id="man-score" /></td><td><a href="javascript:;" onclick="manualAddCase();return false;">[+]</a></td></tr>
</tfoot>
</table>
<div><h3>Batch Add Test Cases</h3><p>Use (*) to represent serial number</p>
<p>Input: <input id="bat-input" value="data(*).in" onkeyup='caseShowPreview()' /> Answer: <input id="bat-answer" value="data(*).out" onkeyup='caseShowPreview()' /> Time Limit: <input id="bat-tl" value="1" /> Memory Limit: <input id="bat-ml" value="128" /> Score: <input id="bat-score" value="10" /></p>
<p>From: <input id="bat-from" value="1" size="6" maxlength="3" onkeyup='caseShowPreview()' /> To: <input id="bat-to" value="10" size="6" maxlength="3" onkeyup='caseShowPreview()' /> Length: <input id="bat-len" value="2" size="3" maxlength="2" onkeyup='caseShowPreview()' /></p><p><input type='button' value='Batch Add' onclick='batchAddCase()' /></p>
<p>Examples</p><ul id='bat-examples'></ul></div>
</td></tr>
<tr></tr>
</table>
</form>
{/block}
{block name="column-right"}
{include file="admin_sidebar.tpl"}
{/block}