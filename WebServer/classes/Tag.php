<?php
class Tag extends ActiveRecord
{
	public static $keyProperty = 'id';
	public static $schema = array(
		'id' => array('class' => 'int'),
		'tag' => array('class' => 'string'),
		'count' => array('class' => 'int','query' => 'count(`id`)'));
	public static $tableName = 'oj_tags';
	
	public static function AddIfNotExist($t)
	{
		$tag = Tag::first(array('id','tag'),'WHERE `tag` = ?',array($t));
		if (!$tag)
		{
			$tag = new Tag();
			$tag->tag = $t;
			$tag->add();
		}
		return $tag;
	}
}
?>