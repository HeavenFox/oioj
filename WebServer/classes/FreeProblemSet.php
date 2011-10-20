<?php
class FreeProblemSet
{
	public function parse()
	{
		$resource = xml_parser_create();
		
		xml_parser_free($resource);
	}
}
?>