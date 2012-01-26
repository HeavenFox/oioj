<?php
class Tag extends ActiveRecord
{
	static $keyProperty = 'id';
	static $schema = array('id' => array('class' => 'int'),'tag' => array('class' => 'string'),'count' => array('class' => 'int', 'query' => 'count(`id`)'));
}
?>