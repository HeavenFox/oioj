{extends file="base.tpl"}
{block name="body"}
<h2>Submit Form</h2>
<p>You can paste your code here. This is mainly intended for mobile platforms where file submission is not possible, e.g. iPad.</p>
<form method="post" action="index.php?mod=submit&solution=1">
<table>
<tr>
    <td>Problem ID:</td><td><input name="id" type='number' {if isset($id)}value='{$id}'{/if} /></td>
</tr>
<tr>
    <td>Language:</td><td> <select name='lang'>
    <option value='cpp'>C++</option>
    <option value='c'>C</option>
    <option value='pas'>Pascal</option></select></td>
</tr>
<tr>
    <td>Code:</td><td><textarea name="code"></textarea></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" value="Submit" /></td>
</tr>
</table>
</form>
{/block}