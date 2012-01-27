<?php
import('ActiveRecord');
import('Tag');

class TaggedRecord extends ActiveRecord
{
	public static $tagAssocTable = array('_table_name','record_column','tag_column');
	
	public static function GetByTag($properties, $tagId, $suffix = '')
	{
		return static::queryByTags($properties,array($tagId),$suffix);
	}
	
	/**
	 * Query records with tags using Unioned-Intersect query
	 *
	 * @param array $properties Properties
	 * @param array $cond Condition array
	 * @param string $suffix Query suffix, like LIMIT or ORDER BY
	 */
	public static function queryByTags($properties, $cond, $suffix = '')
	{
		$queries = array();
		$single = array();
		// Squash all single items and make queries for others
		foreach ($cond as $v)
		{
			if (is_int($v))
			{
				$single[] = $v;
			}else if (count($v) == 1)
			{
				$single[] = $v[0];
			}else
			{
				$queries[] = self::_makeAllInSetQueryString($properties, $v);
			}
		}
		
		if (count($single))
		{
			$queries[] = self::_makeAnyInSetQueryString($properties, $single);
		}
		
		$query = implode(' UNION ', $queries);
		
		$query .= (' '.$suffix);
		$stmt = Database::Get()->query($query);
		
		$resultSet = array();
		
		foreach ($stmt as $row)
		{
			$obj = new static;
			$obj->_fillRow($row, $properties);
			$resultSet[] = $obj;
		}
		
		return $resultSet;
	}
	
	public static function GetPopularTags($num = null)
	{
		return Tag::find(array('id','tag','count'),'INNER JOIN `'.static::$tagAssocTable[0].'` ON (`'.static::$tagAssocTable[0].'`.`'.static::$tagAssocTable[2].'` = `'.Tag::$tableName.'`.`'.Tag::$keyProperty.'`) GROUP BY `'.Tag::$tableName.'`.`'.Tag::$keyProperty.'` ORDER BY count(*) DESC'.($num ? " LIMIT 0,{$num}" : ''));
	}
	
	protected static function _makeAllInSetQueryString($properties, $tags)
	{
		if (is_int($tags))return self::_makeAnyInSetQueryString($properties, array($tags));
		
		$query = 'SELECT '.parent::_makeColumnList($properties).' FROM ';
		for ($i = 1; $i <= count($tags); $i++)
		{
			if ($i != 1)
			{
				$query .= ' CROSS JOIN ';
			}
			$query .= Tag::Table() ." AS `_tag_{$i}`";
		}
		
		for ($i = 1; $i <= count($tags); $i++)
		{
			$query .= ' INNER JOIN `' . static::$tagAssocTable[0] . "` AS `_assoc_{$i}` ON (`_assoc_{$i}`.`".static::$tagAssocTable[2]."` = `_tag_{$i}`.`id` ";
			if ($i > 1)
			{
				$query .= " AND `_assoc_{$i}`.`".static::$tagAssocTable[1]."` = `_assoc_1`.`".static::$tagAssocTable[1]."`";
			}
			$query .= ')';
		}
		
		$query .= ' INNER JOIN '.static::Table().' ON (`_assoc_1`.`'.static::$tagAssocTable[1].'` = '.static::Column(static::$keyProperty).') ';
		
		$query .= ' WHERE ';
		for ($i = 1; $i <= count($tags); $i++)
		{
			if ($i != 1)
			{
				$query .= ' AND ';
			}
			$query .= "`_tag_{$i}`.`id` = ".$tags[$i-1];
		}
		return $query;
	}
	
	protected static function _makeAnyInSetQueryString($properties, $tags)
	{
		if (is_int($tags))$tags = array($tags);
		return parent::_makeQueryString($properties, ' RIGHT JOIN `'.static::$tagAssocTable[0].'` ON (`'.static::$tagAssocTable[0].'`.`'.static::$tagAssocTable[1].'` = '.static::Column(static::$keyProperty).') WHERE `'.static::$tagAssocTable[0].'`.`'.static::$tagAssocTable[2]. (count($tags) == 1 ? ('` = '.$tags[0]) : '` IN ('.implode(',',$tags).')'));
	}
}
?>