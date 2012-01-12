var fancyclosed = false;

function checkRecord(id)
{
	$('#submit_infobox').hide(500);
	jQuery.getJSON('index.php?mod=records&ajax=1&id='+id+"&rand="+Math.random(),function(data){
		//console.log(data);
		$('#submit_infobox').html(data.content);
		if (!data.finished && !fancyclosed)
		{
			setTimeout('checkRecord('+id+')',3000);
		}
		$('#submit_infobox').show(500);
	});
}

(function($){
	$.fn.submitsoln_prob = function(params)
	{
		newParams = jQuery.extend({url: 'index.php?mod=submit&solution=1&ajax=1',
				handler: function(data){
				setTimeout('checkRecord('+data.result.record_id+')',3000);
				$.fancybox("<div id='submit_infobox'><p>Congratulations. Your program has been submitted successfully.</p><p>Record Information</p><p>ID: "+data.result.record_id+"<br />Server:"+data.result.server_name+"</p><p>We will automatically display update information here, or, if you prefer, click <a href='#' onclick='popupRecord("+data.result.record_id+");return false;'>here</a> to display a popup window.</p></div>",{
						'autoDimensions'	: false,
						'width'     : 350,
						'height'    : 250,
						'scrolling'	: 'no',
						'onClosed'  : function(){fancyclosed=true;}});
				
			}},params);
		this.submitsoln(newParams);
		return this;
	}
})(jQuery);