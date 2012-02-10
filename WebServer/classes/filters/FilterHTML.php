<?php
import('filters.Filter');
class FilterHTML extends Filter
{
	protected function doSanitize($data)
	{
		require_once LIB_DIR . 'htmlpurifier/HTMLPurifier.standalone.php';
		$p = new HTMLPurifier(HTMLPurifier_Config::createDefault());
		return $p->purify($data);
	}
}
?>