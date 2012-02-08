<div id='comments_list'>
{$comments_html|default:"<div id='none'>None Yet. Be the first!</div>"}
</div>
{pager cur=$curPage max=$maxPage script="commentFlipPage(%s)"}