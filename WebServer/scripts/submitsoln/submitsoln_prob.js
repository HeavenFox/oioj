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
	$.fn.submitsoln_prob = function(params)
	{
		this.submitsoln({
			url: 'index.php?mod=submit&solution=1&ajax=1',
			drop: params.drop,
			handler: function(){
				$.fancybox("<div id='submit_infobox'>ID: "+data.result.record_id+"<br />Server:"+data.result.server_name+"</div>",{
						'autoDimensions'	: false,
						'width'     : 350,
						'height'    : 250,
						'scrolling'	: 'no'});
				setTimeout('checkRecord('+data.result.id+')',3000);
			}
		});
		return this;
	}
})(jQuery);