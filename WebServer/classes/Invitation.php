<?php
import('ActiveRecord');

class Invitation extends ActiveRecord
{
	static $tableName = 'oj_invitations';
	static $schema = array(
		'id' => array('class' => 'int'),
		'code' => array('class' => 'string'),
		'sender' => array('class' => 'User', 'comp' => 'one','column' => 'sender'),
		'user' => array('class' => 'User', 'comp' => 'one','column' => 'user')
	);
}
?>