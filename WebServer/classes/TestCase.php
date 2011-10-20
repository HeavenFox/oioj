<?php
import('ActiveRecord');

class TestCase extends ActiveRecord
{
	public static $schema = array(
		'problem' => array('class' => 'Problem', 'comp' => 'one', 'column' => 'pid'),
		'cid' => array('class' => 'int'),
		'input' => array('class' => 'string'),
		'answer' => array('class' => 'string'),
		'timelimit' => array('class' => 'double'),
		'memorylimit' => array('class' => 'int'),
		'score' => array('class' => 'int')
	);
	
	public static $tableName = 'oj_testcases';
	
	public $inputContent;
	public $answerContent;
}
?>