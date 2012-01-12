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

var currentBannerBullet;

function moveBannerTo(n)
{
	$("#bn_banners").css("top",-(n-1)*360+"px");
	//currentBannerBullet = $('')
}