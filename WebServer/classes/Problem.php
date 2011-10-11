<?php
import('ActiveRecord');

class Problem extends ActiveRecord
{
	public static $schema = array(
			'id' => array('class' => 'int'),
			'title' => array('class' => 'string'),
			'body' => array('class' => 'text'),
			'type' => array('class' => 'int'),
			'input' => array('class' => 'string'),
			'output' => array('class' => 'string'),
			'compare' => array('class' => 'string'),
			'submission' => array('class' => 'int'),
			'listing' => array('class' => 'bool'),
			'user' => array('class' => 'User', 'comp' => 'one', 'column' => 'uid')
		);
	public static $tableName = 'oj_problems';
	public static $keyProperty = 'id';
	
	
}
?>