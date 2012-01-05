var timeOutId;

function checkRecord(id)
{
	alert('');
	jQuery.get('index.php?mod=records&ajax=1&id='+id,function(data){
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
			handler: function(data){
				timeOutId = setTimeout('checkRecord('+data.result.record_id+')',3000);
				$.fancybox("<div id='submit_infobox'><p>Congratulations. Your program has been submitted successfully.</p><p>Record Information</p><p>ID: "+data.result.record_id+"<br />Server:"+data.result.server_name+"</p><p>We will automatically display update information here, or, if you prefer, click <a href='index.php?mod=records&popup=1&id="+data.result.record_id+"'>here</a> to display a popup window.</p></div>",{
						'autoDimensions'	: false,
						'width'     : 350,
						'height'    : 250,
						'scrolling'	: 'no',
						'onClosed'  : clearTimeout(timeOutId)});
				
			}
		});
		return this;
	}
})(jQuery);