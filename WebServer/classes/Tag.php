<?php
class Tag extends ActiveRecord
{
	public static $keyProperty = 'id';
	public static $schema = array(
		'id' => array('class' => 'int'),
		'tag' => array('class' => 'string'),
		'count' => array('class' => 'int','query' => 'count(`id`)'));
	public static $tableName = 'oj_tags';
}
?>