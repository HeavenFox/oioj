<table class='tablist'>
<thead>
	<tr>
	<td width="20px">Rank</td>
	<td>Username</td>
	{if isset($ranking_display_params.total_score)}<td>Score</td>{/if}
	{if isset($ranking_display_params.total_time)}<td>Total Time</td>{/if}
	{if isset($ranking_display_params.max_time)}<td>Last Accepted</td>{/if}
	{if isset($ranking_display_params.num_right)}<td>Num Accepted</td>{/if}
	{if isset($ranking_display_params.num_wrong)}<td>Num Wrong</td>{/if}
	{if isset($ranking_display_params.duration)}<td>Duration</td>{/if}
	</tr>
</thead>
<tbody>
{foreach $ranking as $r}
	<tr class="{cycle values="odd,even"}">
<td>{$r->rank}</td>
	<td><a href="{$r->id}">{$r->username}</a></td>
	{if isset($ranking_display_params.total_score)}<td>{$r->rankingParams.total_score}</td>{/if}
	{if isset($ranking_display_params.total_time)}<td>{$r->rankingParams.total_time|duration_format}</td>{/if}
	{if isset($ranking_display_params.max_time)}<td>{$r->rankingParams.max_time}</td>{/if}
	{if isset($ranking_display_params.num_right)}<td>{$r->rankingParams.num_right}</td>{/if}
	{if isset($ranking_display_params.num_wrong)}<td>{$r->rankingParams.num_wrong}</td>{/if}
	{if isset($ranking_display_params.duration)}<td>{$r->rankingParams.duration|duration_format}</td>{/if}
	</tr>
{/foreach}
</tbody>
</table>