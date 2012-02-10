(function($){
	$.fn.submitsoln = function(params)
	{
		that = this;
		this.fileupload({
			dataType: 'json',
			url: params.url,
			dropZone: params.drop,
			formData: $(that).find('form').first().serializeArray(),
			paramName: 'source',
			fail: function(e, data){
				if (params.uploadStatus)
				{
					params.uploadStatus.html('Failed to upload file');
				}
			},
			send: function(e, data){
				if (params.uploadStatus)
				{
					params.uploadStatus.show().html('Uploading... Please Wait');
				}
				that.find('input[type=submit]').attr('disabled','disabled');
			},
			always: function(e, data)
			{
				if (params.uploadStatus)
				{
					params.uploadStatus.hide().html('');
				}
				that.find('input[type=submit]').removeAttr('disabled');
			},
			done: function(e, data)
			{
				if (data.result.error)
				{
					alert(data.result.error);
				}
				else
				{
					params.handler(data);
				}
			}
			
		});
		
		if (params.hoverClass)
		{
			$(document).bind('dragover', function (e) {
		    var dropZone = params.drop,timeout = window.dropZoneTimeout;
		    if (!timeout) {
		        dropZone.addClass(params.inClass);
		    } else {
		        clearTimeout(timeout);
		    }
		    if (e.target === dropZone[0]) {
		        dropZone.addClass(params.hoverClass);
		    } else {
		        dropZone.removeClass(params.hoverClass);
		    }
		    window.dropZoneTimeout = setTimeout(function () {
		        window.dropZoneTimeout = null;
		        dropZone.removeClass(params.inClass +" " + params.hoverClass);
		    }, 100);
		});
		}
		return this;
	}
})(jQuery);