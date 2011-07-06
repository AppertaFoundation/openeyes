<?php

/**
 * A class that all OpenEyes active record classes should extend.
 *
 * Currently its only purpose is to remove all html tags to
 * prevent XSS.
 */
class BaseActiveRecord extends CActiveRecord
{
	/**
	 * Strips all html tags out of attributes to be saved.
	 *
	 * @param boolean $runValidation
	 * @param array $attributes
	 * @return boolean
	 */
	public function save($runValidation=true,$attributes=null)
	{
		$primaryKey = $this->tableSchema->primaryKey;
		foreach ($this->attributes as $name => $value) {
// @todo - there is a lot going on here, make sure it does exactly what it should! The '!empty' check is
// to prevent it populating NULL values, e.g. episode.end_date was changing from NULL to 0000-00-00 00:00:00.
			if (!empty($value) && ($primaryKey !== $name || 
				(is_array($primaryKey) && !in_array($name, $primaryKey)))) {
				$this->$name = strip_tags($value);
			}
		}

		return parent::save($runValidation, $attributes);
	}
}
