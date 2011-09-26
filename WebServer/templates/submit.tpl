{extends file="base.tpl"}
{block name="body"}
<form method="post" action="judge.php">
<table>
<tr>
    <td>User ID:</td><td><input name="uid" /></td>
</tr>
<tr>
    <td>Problem ID:</td><td><input name="pid" /></td>
</tr>
<tr>
    <td>Language:</td><td> <input name="lang" /></td>
</tr>
<tr>
    <td>Code:</td><td><textarea name="code">Hello World</textarea></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" value="Submit" /></td>
</tr>
</table>
</form>
{/block}