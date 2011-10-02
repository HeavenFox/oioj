<form>
<table>
<tr><td><label for="title">Title</label></td><td>
  <input type="text" name="title" id="title" /></td></tr>
<tr><td><label for="input">Body</label></td><td>
  <input type="text" name="body" id="body" /></td></tr>
<tr><td><label for="input_file">Input</label></td><td>
    <input type="text" name="input_file" id="input_file" />
    <input type="checkbox" name="screen_input" id="screen_input" onchange="toggleScreen(this,'input_file')" />
    <label for="screen_input">Screen</label></td></tr>
<tr><td><label for="output_file">Output</label></td><td>
    <input type="text" name="output_file" id="output_file" />
    <input type="checkbox" name="screen_output" id="screen_output" onchange="toggleScreen(this,'output_file')" />
    <label for="screen_output">Screen</label></td></tr>
<tr><td><label for="comp_method">Compare Method</label></td><td>
    <select name="comp_method" id="comp_method">
      <option value="/FULLTEXT/" selected="selected">Full Text</option>
      <option value="/OMITSPACE/">Omit Spaces at Line Ends</option>
      <option>Special Judge</option>
    </select>
    <label for="special_judge">bin name</label>
    <input type="text" name="special_judge" id="special_judge" /></td></tr>
<tr><td>Test Cases</td><td>
<table>
<thead><tr><td>Input</td><td>Answer</td><td>Time Limit (s)</td><td>Memory Limit (MB)</td><td>Score</td><td></td></tr></thead>
<tbody>
<tr></tr>
</tbody>
<tfoot>
<tr><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><a href="javascript:;">[+]</a></td></tr>
</tfoot>
</table>
<div><h3>Batch Add Test Cases</h3><p>Use (*) to represent serial number</p><p>Input: <input value="data(*).in" onchange="" /> Answer: <input value="data(*).out" onchange="" /> From: <input value="1" size="6" maxlength="3" /> To: <input value="10" size="6" maxlength="3" /> Length: <input value="2" size="6" maxlength="2" /></p><p><input type='button' value='Batch Add' /></p><p>Examples</p><ul></ul></div>
</td></tr>
<tr></tr>
</table>
</form>