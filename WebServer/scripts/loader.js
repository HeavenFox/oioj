var loader = new Object();

loader.totalFrame = 12;
loader.width = 40;
loader.height = 40;
loader.onScreen = false;

loader.animate = function(frame)
{
	if (frame == this.totalFrame) frame=0;
	$('#loading_indicator').css('background-position',"0 "+(-this.height*frame)+"px");
	this.timeout = setTimeout(function(){
		loader.animate(frame+1);
	},50);
};

loader.show = function()
{
	if (!this.onScreen)
	{
		this.onScreen = true;
		$('<div id="loading_indicator" />').appendTo($('body'));
		this.animate(0);
	}
};

loader.hide = function()
{
	if (this.onScreen)
	{
		this.onScreen = false;
		$('#loading_indicator').remove();
		clearTimeout(this.timeout);
	}
}