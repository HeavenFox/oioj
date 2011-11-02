var captchaShown = false;

function showCAPTCHA()
{
	if (!captchaShown)
	{
		captchaShown = true;
		$.get("index.php?mod=user&act=getcaptcha",function(data){
			
			$("#captcha").append(data);
		});
	}
}

function moveBannerTo(loc)
{
	$("#bn_banners").css("top",loc);
}