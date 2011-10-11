var captchaShown = false;

function showCAPTCHA()
{
	if (!captchaShown)
	{
		captchaShown = true;
		$.get("index.php?mod=user&act=getcaptcha",function(data){
			alert(data);
			$("#captcha").append(data);
		});
	}
}