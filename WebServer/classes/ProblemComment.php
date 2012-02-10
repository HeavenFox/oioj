<?php
import('ActiveRecord');
class ProblemComment extends ActiveRecord
{
	static $schema = array(
		'id' => array('class'=>'int'),
		'parentID' => array('class'=>'int','column'=>'parent'),
		'user' => array('class' => 'User','comp' => 'one', 'column' => 'uid'),
		'problem' => array('class' => 'Problem', 'comp' => 'one', 'column' => 'pid'),
		'content' => array('class' => 'text'),
		'timestamp' => array('class' => 'int')
	);
	static $tableName = 'oj_problem_comments';
	
	public $children;
}
?>