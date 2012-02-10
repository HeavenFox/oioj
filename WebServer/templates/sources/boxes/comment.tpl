<div class="comment_container" id="comment-{$comment->id}">
<div class="comment_poster">
<img src="http://www.gravatar.com/avatar/{md5(strtolower(trim($comment->user->email)))}?s=60&d=mm" title="" width="60px" height="60px" /><br />
<div class="username">{$comment->user->username}</div>
<div class="posttime">{$comment->timestamp|date_format}</div>
</div>
<div class="comment_main">
<div class="comment_content">
{$comment->content}
</div>
<div class="comment_action">
<a href='#postcomment' onclick='replyComment({$comment->id})'>Reply</a>
</div>
</div>
<div style='clear: both'></div>
</div>