<?php
import('ActiveRecord');
import('Tag');

class TaggedRecord extends TaggedRecord
{
	public static $tagAssocTable = array('_table_name','record_column','tag_column');
	
	public function getTags()
	{
		
	}
	public static function getByTag($properties, $composites = null, $tagId = null)
	{
		if (is_int($tagId))
		{
			return static::queryByTags($properties,$composites,strval($tagId));
		}
		if (is_array($tagId))
		{
			return static::queryByTags($properties,$composites,implode(' || '));
		}
	}
	
	/**
	 * Query records with tags using Unioned-Intersect query
	 *
	 * @param array $properties Properties
	 * @param array $cond Condition array: 
	 */
	public static function queryByTags($properties, $cond)
	{
		$queries = array();
		// Squash all single items
		foreach ($
		
		
		return static::find($properties,$suffix);
	}
	
	public static function getTags()
	{
		return Tag::find(array('id','tag'),null,'LEFT JOIN `'.static::$tagAssocTable[0].'` ON (`'.static::$tagAssocTable[0].'`.`'.static::$tagAssocTable[2].'` = `'.static::$tableName.'`.`'.static::$keyProperty.'`) WHERE `'.static::$tagAssocTable[0].'`.`'.static::$tagAssocTable[1].'` IS NOT NULL GROUP BY `'.Tag::$tableName.'`.`'.Tag::$keyProperty.'` ORDER BY count(*) DESC');
	}
	
	protected static function _makeAllInSetQueryString($properties, $tags)
	{
		
	}
}
?>