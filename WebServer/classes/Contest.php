<?php
import('ActiveRecord');
class Contest extends ActiveRecord
{
	static $tableName = 'oj_contests';
	static $schema = array(
		'id' => array('class' => 'int'),
		'title' => array('class' => 'string'),
		'description' => array('class' => 'text'),
		'startTime' => array('class' => 'int','column' => 'start_time'),
		'endTime' => array('class' => 'int','column' => 'end_time'),
		'regDeadline' => array('class' => 'int','column' => 'reg_deadline'),
		'user' => array('class' => 'User','comp' => 'one','column' => 'uid'),
		'problems' => array(),
		'registrants' => array('class' => 'User', 'comp' => '')
	);
	
	
}
?>