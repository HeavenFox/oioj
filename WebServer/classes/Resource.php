<?php
class Resource extends ActiveRecord
{
	static $tableName = 'oj_resources';
	
	static $schema = array(
		'id' => array('class' => 'int'),
		'title' => array('class' => 'string'),
		'description' => array('class' => 'text'),
		'tags' => array('class' => 'Tag', 'comp' => 'junction', 'column' => array('rid','tid'))
	);
}
?>