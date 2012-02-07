<div id='comments_list'>
{$comments_html|default:"None"}
</div>
{pager cur=$curPage max=$maxPage script="commentFlipPage(%s)"}