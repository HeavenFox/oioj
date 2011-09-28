<?php
import('ActiveRecord');

class Problem extends ActiveRecord
{
	public function __construct()
	{
		parent::__construct();
		$this->_tableName = 'oj_problems';
		$this->_properties = array('id' => 'id', 'title' => 'title', 'body' => 'body', 'type' => 'type', 'input' => 'input', 'output' => 'output', 'compare' => 'compare', 'submission' => 'submission', 'accepted' => 'accepted', 'listing' => 'listing');
		$this->_propertiesClass = array('')
	}
}
?>