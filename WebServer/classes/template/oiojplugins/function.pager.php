<?php
// {pager cur=3 max=10 adj=4 url="index.php?page=%d" form="index.php?" var="page" script=""}
function smarty_function_pager($params, Smarty_Internal_Template $smarty)
{
	$def = array('cur' => 1, 'max' => 1, 'adj' => 3, 'var' => 'page');
	
	foreach ($def as $k => $v)
	{
		if (!isset($params[$k]))
		{
			$params[$k] = $v;
		}
	}
	
	$html = '<div class="pager"><ul>';
	
	$beg = $params['cur'] - $params['adj'];
	if ($beg < 1)
	{
		$beg = 1;
	}
	
	$end = $params['cur'] + $params['adj'];
	if ($end > $params['max'])
	{
		$end = $params['max'];
	}
	
	$t = function ($val) use ($params)
	{
		$html = '<li'.($val == $params['cur'] ? ' class="current"' : '').'>';
		
		if ($val != $params['cur'])
		{
			$html .= '<a href="'.(isset($params['url']) ? sprintf($params['url'],$val) : 'javascript:;').'"'.(isset($params['script']) ? ' onclick="'.sprintf($params['script'],$val).'"' : '').'>';
		}
		$html .= $val;
		if ($val != $params['cur'])
		{
			$html .= '</a>';
		}
		$html .= '</li>';
		return $html;
	};
	
	if ($beg > 1)
	{
		$html .= $t(1);
		$html .= '<li class="ellipsis">...</li>';
	}
	
	for ($i = $beg; $i <= $end; $i++)
	{
		$html .= $t($i);
	}
	
	if ($end < $params['max'])
	{
		$html .= '<li class="ellipsis">...</li>';
		$html .= $t($params['max']);
	}
	
	$html .= '</ul>';
	if (isset($params['form']) || isset($params['script']))
	{
		$html .= '<form'.(isset($params['form']) ? 'action="'.$params['form'].'" method="POST"' : '').(isset($params['script']) ? ' onsubmit="'.sprintf($params['script'],'parseInt(this.'.$params['var'].'.value)').';return false;"' : '').'><input type="number" name="'.$params['var'].'" min="1" max="'.$params['max'].'" step="1" style="width: 55px" /><input type="submit" value="Go" /></form></div>';
	}
	return $html;
}
?>