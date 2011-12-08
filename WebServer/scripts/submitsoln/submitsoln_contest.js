(function($){
	$.fn.submitsoln_contest = function(params)
	{
		this.submitsoln({
			url: 'index.php?mod=contestproblem&act=submit&ajax=1',
			drop: params.drop,
			
			handler: function(){
					$.fancybox("<div id='submit_infobox'>Your submission has been received. To update, submit a new solution again.</div>",{
						'autoDimensions'	: false,
						'width'     : 350,
						'height'    : 250,
						'scrolling'	: 'no'});
			}
		});
		return this;
	}
})(jQuery);