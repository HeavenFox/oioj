<?php
import('ActiveRecord');

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
	 * Query records with tags matching certain condition
	 *
	 * @param array $properties Properties
	 * @param array $composites Composites
	 * @param string $cond Condition string
	 */
	public static function queryByTags($properties, $composites = null, $cond)
	{
		$cond = preg_replace('/[^0-9()|&! ]+/','',$cond)
		$suffix = 'LEFT JOIN `'.static::$tagAssocTable[0].'` USING `'.static::$tableName.'`.`'.static::$keyProperty.'` = `'.static::$tagAssocTable[2].'` WHERE ';
		$where = str_replace('||',' OR ',$cond);
		$where = str_replace('&&',' AND ',$cond);
		$where = str_replace('!',' NOT ',$cond);
		$suffix .= preg_replace('/([0-9]+)/','`'.static::$tagAssocTable[0].'`.`'.static::$tagAssocTable[3].'` = \1',$where);
		return static::find($properties,$composites,$suffix);
	}
	
	public static function getTags()
	{
		return Tag::find(array('id','tag'),null,'LEFT JOIN `'.static::$tagAssocTable[0].'` ON (`'.static::$tagAssocTable[0].'`.`'.static::$tagAssocTable[2].'` = `'.static::$tableName.'`.`'.static::$keyProperty.'`) WHERE `'.static::$tagAssocTable[0].'`.`'.static::$tagAssocTable[1].'` IS NOT NULL GROUP BY `'.Tag::$tableName.'`.`'.Tag::$keyProperty.'` ORDER BY count(*) DESC');
	}
}
?>