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

var curBanner = 1;
var totalBanner;
var bannerTimeout;

function moveBannerTo(n)
{
	$("#bn_banners").css("top",-(n-1)*360+"px");
	$("#bn_links a img").css("width","10px").css("height","10px").css("margin","2px");
	$("#bn_links a:nth-child("+n+")").children().css("width","14px").css("height","14px").css("margin","0px");
}

function autoMoveBanner()
{
	curBanner++;
	if (curBanner > totalBanner)curBanner = 1;
	moveBannerTo(curBanner);
	setAutoBanner();
}

function setAutoBanner()
{
	bannerTimeout = setTimeout('autoMoveBanner()', 10000);
}

function stopAutoBanner()
{
	clearTimeout(bannerTimeout);
}

$(function(){
	totalBanner = $("#bn_links a").size();
	moveBannerTo(curBanner);
	setAutoBanner();
});

