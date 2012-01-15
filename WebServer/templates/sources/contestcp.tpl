{extends file="base.tpl"}
{block name="body"}
<h2>{$contest->title} - CP</h2>
{if $status_code == Contest::STATUS_WAITING}

<h3>Status: Waiting.</h3>

<a href='index.php?mod=contestcp&amp;id={$contest->id}&amp;act=start'>Begin Now</a>
Begin Registration Now
Stop Registration Now
{/if}

{if $status_code == Contest::STATUS_INPROGRESS}
<h3>Status: In Progress.</h3>

<a href='index.php?mod=contestcp&amp;id={$contest->id}&amp;act=end'>End Contest Now</a>
{/if}

{if $status_code == Contest::STATUS_FINISHED}
<h3>Status: Finished</h3>
<p>Next Step</p>
<a href='index.php?mod=contestcp&amp;id={$contest->id}&amp;act=judge'>Judge Now</a>
{/if}

{if $status_code == Contest::STATUS_JUDGING}

Status: Judging
<p>Next Step</p>
<a href='index.php?mod=contestcp&amp;id={$contest->id}&amp;act=finishjudge'>Set Status to Judged</a>
{/if}

{if $status_code == Contest::STATUS_JUDGED}
Status: Judged.
{/if}

{if isset($num_judged)}
<h3>Judge Progress</h3>
<p>{$num_tojudge} submissions are to be judged (those not superseded by newer submission), constituting a {($num_tojudge*100/$num_submissions)|string_format:"%.1f"}% of total submission. Among them, {$num_judged} ({($num_judged*100/$num_tojudge)|string_format:"%.1f"}%) submissions are judged so far.</p>
{/if}


<h3>Stats</h3>
<ul>
<li>Number of Participants: {$num_participants}</li>
<li>Number of Participants with Submission: {$num_participants_wsub} ({($num_participants_wsub/$num_participants*100)|string_format:"%.1f"}%)</li>
<li>Number of Participants who have begun: {$num_participants_started} ({($num_participants_started/$num_participants*100)|string_format:"%.1f"}%)</li>
<li>Number of Submissions: {$num_submissions} (avg. {($num_submissions/$num_participants)|string_format:"%.1f"} per participant)</li>
</ul>

{if isset($analysis)}
<h3>Problem Analysis</h3>
<table>
<thead>
<tr><td>ID</td><td>Title</td><td>Users Submitted</td><td>Average Score</td><td>Maximum Score</td></tr>
</thead>
<tbody>
{foreach $analysis as $row}
<tr><td>{$row.pid}</td><td>{$row.title}</td><td>{$row.users} ({($row.users*100/$num_participants)|string_format:"%.1f"}%)</td><td>{$row.average|string_format:"%.2f"}</td><td>{$row.maximum}</td></tr>
{/foreach}
</tbody>
</table>
{/if}
{/block}