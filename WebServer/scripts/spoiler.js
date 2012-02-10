$.fn.parseSpoiler = function()
{
	this.find('div.spoiler').hide().wrap("<div class='spoiler_wrapper' />").before($('<a href="javascript:;">Spoiler Alert! To view, click here</a>').bind("click",function(){
		$(this).next().toggle(500);
	}));
	return this;
};

$(function()
{
	$(document).parseSpoiler();
});