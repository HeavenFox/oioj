<?php
import('filters.Filter');
class FilterSpoiler extends Filter
{
	protected function doSanitize($data)
	{
		return str_replace('<spoiler>','<div class="spoiler">',str_replace('</spoiler>','</div>',$data));
	}
}