<h3>Basic Information</h3>
<p>Record ID: {$id}</p>
<p>Status: {$status}</p>
<p>Server Name: {$server_name}</p>
{if $results_available}
<h3>Results</h3>
<p>Score: {$score}</p>
<p>Test Cases</p>
<ul>
{foreach $cases as $v}
<li>{$v}</li>
{/foreach}
</ul>
{/if}