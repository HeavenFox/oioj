(function($){
	$.fn.submitsoln = function(params)
	{
		that = this;
		this.fileupload({
			dataType: 'json',
			url: params.url,
			dropZone: params.drop,
			paramName: 'source',
			fail: function(e, data){console.log(data);},
			send: function(e, data){that.find('input[type=submit]').attr('disabled','disabled');},
			always: function(e, data){that.find('input[type=submit]').removeAttr('disabled');},
			done: function(e, data){
				console.log(data);
				if (data.result.error)
				{
					alert(data.result.error);
				}
				else
				{
					params.handler();
				}
			},
			
		});
		return this;
	}
})(jQuery);