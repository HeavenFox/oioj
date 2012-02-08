{extends file="problem.tpl"}
{block name="html_head" append}
<link rel='stylesheet' href='templates/pager.css' />
<link rel='stylesheet' href='templates/comments.css' />
<script type="text/javascript">
var curPage = {$curPage};
</script>
<script type="text/javascript" src="scripts/comments.js"></script>
<script type="text/javascript" src="scripts/spoiler.js"></script>
{/block}
{block name="titlebar_links"}
<div class="links"><a href='index.php?mod=problem&id={$problem->id}'>Problem</a>&nbsp;&nbsp;Discussion&nbsp;&nbsp;<a href=''>Solution</a></div>
{/block}
{block name="problem_body"}
<div id='comments'>
{$html}
</div>
{ifable to="submit_problem_comment"}
<a name="postcomment"></a>
<div id='postcomment'>
<h2>Add Comment</h2>
<form onsubmit="submitComment(this);return false;"><input type='hidden' name='parent' value="0" />
<p id="reply_indicator" class="hidden">You are now replying to a comment. <a href="javascript:;" onclick="clearReply()">Cancel</a></p>
<p>Logged in as: {$current_user->username}</p>
<p>Please, if your comment contains any spoiler, wrap them with &lt;spoiler&gt;...&lt;/spoiler&gt;</p>
<p><textarea name="content"></textarea></p>
<p><input type="submit" value="Post" /></p>
</form>
</div>
{endif}
{/block}