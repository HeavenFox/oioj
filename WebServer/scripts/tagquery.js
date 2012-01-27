(function($){
	$.fn.intersectGroup = function()
	{
		this.droppable({
			drop: function(event, ui)
			{
				if ($(this).hasClass('ig_new'))
				{
					$(this).after('<div class="union"></div><div class="intersect_group ig_new"><ul></ul></div>');
					$(this).next().css({ 'marginLeft' : 100, 'opacity': 0.0 }).animate({ 'marginLeft' : 0, 'opacity': 1 },1000);
					$(this).next().next().css({ 'opacity': 0.0 }).animate({ 'opacity': 1 },1000);
					$(this).next().next().intersectGroup();
					$(this).removeClass('ig_new');
				}
				ui.helper.css({ left: 0, top:0 }).detach().wrap("<li />").parent().appendTo($(this).children());
			}
		});
	};
	
	$.fn.intersectGroupData = function()
	{
		var data = new Array();
		this.children('.intersect_group').each(function(){
			var cur = new Array();
			$(this).find('.tag').each(function(){
				cur.push(parseInt($(this).data('tid')));
			});
			if (cur.length > 0)
			{
				data.push(cur);
			}
		});
		return data;
	};
})(jQuery);