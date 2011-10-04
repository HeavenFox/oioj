<?php
class Resource extends ActiveRecord
{
	static $tableName = 'oj_resources';
	
	static $schema = array(
		'id' => array('class' => 'int'),
		'title' => array(),
		'description' => array()
	);
}
?>