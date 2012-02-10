{extends file='base.tpl'}
{block name="html_head" append}
<style type="text/css">
h1
{
	font-size: 20px;
	margin-bottom: 5px;
}

#error_box
{
	margin: 10px;
	border: 1px solid #FCE8E8;
	border-radius: 7px;
	padding: 5px 10px 5px 10px;
	box-shadow: 0 0 5px rgba(25,25,25,0.2);
	
background: #ffffff;
background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#e5e5e5));
background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%);
background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%);
background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%);
background: linear-gradient(top,  #ffffff 0%,#e5e5e5 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 );

}
</style>
{/block}
{block name="body"}
<div id="error_box">
<h1>Error</h1>
{$message|escape}
</div>
{/block}
{block name="current_user_info"}
{/block}