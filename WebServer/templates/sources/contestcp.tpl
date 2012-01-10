{extends file="base.tpl"}
{block name="body"}
<h2>{$contest->title} - CP</h2>
{if $status_code == Contest::STATUS_WAITING}

<h3>Status: Waiting.</h3>

<a href='index.php?mod=contestcp&amp;cid={$contest->id}&amp;act=start'>Begin Now</a>
Begin Registration Now
Stop Registration Now
{/if}

{if $status_code == Contest::STATUS_INPROGRESS}
<h3>Status: In Progress.</h3>

<a href='index.php?mod=contestcp&amp;cid={$contest->id}&amp;act=end'>End Contest Now</a>
{/if}

{if $status_code == Contest::STATUS_FINISHED}
Status: Finished

<a href='index.php?mod=contestcp&amp;cid={$contest->id}&amp;act=judge'>Judge Now</a>
{/if}

{if $status_code == Contest::STATUS_JUDGING}
Status: Judging

<a href='index.php?mod=contestcp&amp;cid={$contest->id}&amp;act=judge'>Judge Now</a>
{/if}

{if $status_code == Contest::STATUS_JUDGED}
Status: Judged.
{/if}

<a href='index.php?mod=admin_contest&act=edit&cid={$contest->id}'>Edit Contest</a>



<h3>Stats</h3>
<ul>
<li>Number of Participants: {$num_participants}</li>
<li>Number of Participants with Submission: {$num_participants_wsub} ({$num_participants_wsub/$num_participants*100|string_format:"%.1f"}%)</li>
<li>Number of Participants who have begun: {$num_participants_started} ({$num_participants_started/$num_participants*100|string_format:"%.1f"}%)</li>
<li>Number of Submissions: {$num_submissions} (avg. {$num_submissions/$num_participants|string_format:"%.1f"} per participant)</li>
</ul>


{if $show_analysis}
<h3>Problem Analysis</h3>
<table>
<thead>
<tr><td>ID</td><td>Title</td><td>Users Submitted</td><td>Average Score</td><td>Maximum Score</td></tr>
</thead>
<tbody>
{foreach $analysis as $row}
<tr><td>{$row.pid}</td><td>{$row.title}</td><td>{$row.users} ({$row.users*100/$num_participants|string_format:"%.1f"}%)</td><td>{$row.average|string_format:"%.2f"}</td><td>{$row.maximum}</td></tr>
{/foreach}
</tbody>
</table>
{/if}
{/block}