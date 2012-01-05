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
			send: function(e, data){console.log("sending");that.find('input[type=submit]').attr('disabled','disabled');},
			always: function(e, data){console.log("done");that.find('input[type=submit]').removeAttr('disabled');},
			done: function(e, data){
				if (data.result.error)
				{
					alert(data.result.error);
				}
				else
				{
					params.handler(data);
				}
			},
			
		});
		return this;
	}
})(jQuery);