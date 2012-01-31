<?php
class ProblemAttachment extends ActiveRecord
{
	static $schema = array(
		'id' => array('class' => 'int'),
		'problem' => array('class' => 'Problem', 'comp' => 'one', 'column' => 'pid'),
		'filename' => array('class' => 'string'),
		'storedname' => array('class' => 'string')
	);
	static $tableName = 'oj_problem_attachments';
}
?>