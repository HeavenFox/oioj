var timeOutId;

function checkRecord(id)
{
	jQuery.get('index.php?mod=records&id='+id,function(data){
		$('#submit_infobox').html(data.result.content);
		if (data.result.finished)
		{
			clearTimeout(timeOutId);
		}
	});
}

(function($){
	$.fn.submitsoln = function(params)
	{
		/*
		var defaultParams = {
			
		};
		
		for (i in params)
		{
			defaultParams[i] = params[i];
		}*/
		
		this.fileupload({
			dataType: 'json',
			url: 'index.php?mod=submit&solution=1',
			dropZone: params.drop,
			paramName: 'source',
			fail: function(e, data){console.log(data);},
			done: function(e, data){
				console.log(data);
				if (data.result.error)
				{
					alert(data.result.error);
				}
				else
				{
					$.fancybox("<div id='submit_infobox'>ID: "+data.result.record_id+"<br />Server:"+data.result.server_name+"</div>",{
						'autoDimensions'	: false,
						'width'     : 350,
						'height'    : 250,
						'scrolling'	: 'no'});
					setTimeout('checkRecord('+data.result.id+')',3000);
				}
			},
			
		});
		return this;
		/*
		var uploader = new qq.FileUploader({
			element: this[0],
				debug: true,
			action: 'index.php?mod=submit&solution=1',
			multiple: false,
			onComplete: function(id, fileName, responseJSON)
			{
				alert(responseJSON["error"]);
				
			}
		});*/
		
	}
})(jQuery);