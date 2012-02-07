<h3>Basic Information</h3>
<p>Record ID: {$id}</p>
<p>Status: {$status}</p>
<p>Server Name: {$server_name}</p>
{if isset($numwaiting)}<p>You are #{$numwaiting} in queue</p>{/if}
{if isset($numsharing)}<p>Currently, the server is processing {$numsharing} requests</p>{/if}
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