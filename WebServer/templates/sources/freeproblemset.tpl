{if $description}
<h2>Description</h2>
{$description}
{/if}
{if $input}
<h2>Input</h2>
{$input}
{/if}
{if $output}
<h2>Output</h2>
{$output}
{/if}
{if $sample_input}
<h2>Sample Input</h2>
<div class='sample_input'>
{$sample_input}
</div>
{/if}
{if $sample_output}
<h2>Sample Output</h2>
<div class='sample_output'>
{$sample_output}
</div>
{/if}
{if $hint}
<h2>Hint</h2>
<div class='hint'>
{$hint}
</div>
{/if}
<h1>Source</h1>
<div class='source'>
</div>