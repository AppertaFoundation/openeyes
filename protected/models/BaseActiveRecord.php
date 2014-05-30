<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * A class that all OpenEyes active record classes should extend.
 *
 * Currently its only purpose is to remove all html tags to
 * prevent XSS.
 */
class BaseActiveRecord extends CActiveRecord
{
	// flag to automatically update related objects on the record
	// (whilst developing this feature, will allow other elements to continue to work)
	protected $auto_update_relations = false;

	/**
	 * If an array of arrays is passed for a HAS_MANY relation attribute, will create appropriate objects
	 * to assign to the attribute. Sets up the afterSave method to saves these objects if they have validated.
	 *
	 * NOTE once a property is set, this magic method will not be called by php for setting it again, unless the property
	 * is unset first.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @throws Exception
	 * @return mixed|void
	 */
	public function __set($name,$value)
	{
		// Only perform this override if turned on for the given model
		if ($this->auto_update_relations
				&& is_array($value) && count($value)
				&& isset($this->getMetaData()->relations[$name])) {
			$rel = $this->getMetaData()->relations[$name];
			$cls = get_class($rel);
			if ($cls == self::HAS_MANY || $cls == self::MANY_MANY) {
				$rel_cls = $rel->className;
				$pk_attr = $rel_cls::model()->getMetaData()->tableSchema->primaryKey;
				// not supporting composite primary keys at this point
				if (is_string($pk_attr)) {
					$m_set = array();
					foreach ($value as $v) {
						if (is_array($v)) {
							// looks like a list of attribute values, try to find or instantiate the classes
							if (array_key_exists($pk_attr, $v) && isset($v[$pk_attr])) {
								$m = $rel_cls::model()->findByPk($v[$pk_attr]);
							}
							else {
								$m = new $rel_cls();
							}
							$m->attributes = array_merge($this->getRelationsDefaults($name), $v);
							// set foreign key on the related object
							$m->{$rel->foreignKey} = $this->getPrimaryKey();
						}
						elseif (is_object($v)) {
							$m = $v;
						}
						else {
							// try to find the instance
							if (!$m = $rel_cls::model()->findByPk($v)) {
								throw new Exception("Unable to understand value " . print_r($v, true) . " for {$name}");
							}
						}
						$m_set[] = $m;
					}
					// reset the value for it to be set by parent method
					$value = $m_set;
				}
			}
		}
		parent::__set($name, $value);
	}

	// relation defaults are properties that need to be set on related models to define the records completely within the database
	// e.g. the side field on a record defines in the database that a record is a left or right record. So we need this attribute
	// to be set when setting the left or right attribute of owning record.
	protected $relation_defaults = array();

	/**
	 * Convenience method wrapper on the relation defaults property
	 *
	 * @param $name
	 * @return array
	 */
	public function getRelationsDefaults($name) {
		if (isset($this->relation_defaults[$name])) {
			return $this->relation_defaults[$name];
		}
		return array();
	}

	/**
	 * Override to use LSB
	 *
	 * @param string $class_name
	 */
	static public function model($class_name = null)
	{
		return parent::model($class_name ?: get_called_class());
	}

	/**
	 * Strips all html tags out of attributes to be saved.
	 * @return boolean
	 */
	protected function beforeSave()
	{
		// Detect nullable foreign keys and replace "" with null (to fix html dropdowns breaking contraints)
		foreach ($this->tableSchema->foreignKeys as $field => $stuff) {
			if ($this->tableSchema->columns[$field]->allowNull && !$this->{$field}) {
				$this->{$field} = null;
			}
		}

		return parent::beforeSave();
	}

	/**
	 * @param boolean $runValidation
	 * @param array $attributes
	 * @param boolean $allow_overriding - if true allows created/modified user/date to be set and saved via the model (otherwise gets overriden)
	 * @return boolean
	 */
	public function save($runValidation=true, $attributes=null, $allow_overriding=false)
	{
		$user_id = null;

		try {
			if (isset(Yii::app()->user)) {
				$user_id = Yii::app()->user->id;
			}
		} catch (Exception $e) {
		}

		if ($this->getIsNewRecord() || !isset($this->id)) {
			if (!$allow_overriding) {
				// Set creation properties
				if ($user_id === NULL) {
					// Revert to the admin user
					$this->created_user_id = 1;
				} else {
					$this->created_user_id = $user_id;
				}
			}
			if (!$allow_overriding || $this->created_date == "1900-01-01 00:00:00") {
				$this->created_date = date('Y-m-d H:i:s');
			}
		}

		try {
			if (!$allow_overriding) {
				// Set the last_modified_user_id and last_modified_date fields
				if ($user_id === NULL) {
					// Revert to the admin user
					// need this try/catch block here to make older migrations pass with this hook in place
					$this->last_modified_user_id = 1;
				} else {
					$this->last_modified_user_id = $user_id;
				}
			}
			if (!$allow_overriding || $this->last_modified_date == "1900-01-01 00:00:00") {
				$this->last_modified_date = date('Y-m-d H:i:s');
			}
		} catch (Exception $e) {
		}

		return parent::save($runValidation, $attributes);
	}

	/**
	 * Save the given objects for the through relation
	 *
	 * @param $name
	 * @param $rel
	 * @param $thru
	 * @param $new_objs
	 * @throws Exception
	 */
	private function afterSaveThruHasMany($name, $rel, $thru, $new_objs)
	{
		$thru_cls = $thru->className;
		// get the criteria from the named relation to apply to the through relation
		$criteria = new CDbCriteria();
		$criteria->addCondition($rel->on);
		$orig_objs = $this->getRelated($thru->name, true, $criteria);
		$orig_by_id = array();

		if ($orig_objs) {
			foreach ($orig_objs as $orig) {
				$orig_by_id[$orig->{$rel->foreignKey}] = $orig;
			}
		}
		$rel_cls = $rel->className;
		$rel_pk_attr = $rel_cls::getMetaData()->tableSchema->primaryKey;

		if ($new_objs) {
			foreach ($new_objs as $new) {
				if ($save = @$orig_by_id[$new->$rel_pk_attr]) {
					unset($orig_by_id[$new->$rel_pk_attr]);
				}
				else {
					$save = new $thru_cls();
				}
				$save->attributes = $this->getRelationsDefaults($name);
				$save->{$thru->foreignKey} = $this->getPrimaryKey();
				$save->{$rel->foreignKey} = $new->getPrimaryKey();
				if (!$save->save()) {
					throw new Exception("unable to save new through relation {$thru->name} for {$name}" . print_r($save->getErrors(), true));
				}
			}
		}

		foreach ($orig_by_id as $orig) {
			if (!$orig->delete()) {
				throw new Exception("unable to delete redundant through relation {$thru->name} with id {$orig->getPrimaryKey()} for {$name}");
			}
		}

	}

	/**
	 * Save objects to the given relation
	 *
	 * @param $name
	 * @param $rel
	 * @param $new_objs
	 * @param $orig_objs
	 * @throws Exception
	 */
	private function afterSaveHasMany($name, $rel, $new_objs, $orig_objs)
	{
		$saved_ids = array();
		if ($new_objs) {
			foreach ($new_objs as $i => $new) {
				$new->{$rel->foreignKey} = $this->getPrimaryKey();
				if (!$new->save()) {
					throw new Exception('Unable to save {$name} item {$i}');
				}
				$saved_ids[] = $new->getPrimaryKey();
			}
		}
		if ($orig_objs) {
			foreach ($orig_objs as $orig) {
				if (!in_array($orig->getPrimaryKey(), $saved_ids)) {
					if (!$orig->delete()) {
						throw new Exception('Unable to delete removed {$name} with pk {$orig->primaryKey}');
					}
				}
			}
		}
	}

	/**
	 * @param $name
	 * @param \CManyManyRelation $rel
	 * @param $new_objs
	 * @param $orig_objs
	 * @throws Exception
	 */
	private function afterSaveManyMany($name, $rel, $new_objs, $orig_objs)
	{
		// get the table name and foreign keys
		$tbl_name = $rel->getJunctionTableName();
		$tbl_keys = $rel->getJunctionForeignKeys();
		if (count($tbl_keys) != 2) {
			throw new Exception('You must extend afterSaveManyMany to support multi key many many relationship');
		}

		$orig_by_id = array();
		if ($orig_objs) {
			foreach ($orig_objs as $orig) {
				$orig_by_id[$orig->getPrimaryKey()] = $orig;
			}
		}
		// array of ids that should be saved
		if ($new_objs) {
			foreach ($new_objs as $new) {
				$pk = $new->getPrimaryKey();
				if (@$orig_by_id[$pk]) {
					unset($orig_by_id[$pk]);
				}
				else {
					// insert statement
					$builder = $this->getCommandBuilder();
					$criteria = new CDbCriteria();
					$cmd = $builder->createInsertCommand($tbl_name, array($tbl_keys[0] => $this->getPrimaryKey(), $tbl_keys[1] => $new->getPrimaryKey()));
					if (!$cmd->execute()) {
						throw new Exception("unable to insert many to many record for relation {$name} with pk {$new->getPrimaryKey()}");
					}

				}
			}
		}

		foreach (array_keys($orig_by_id) as $remove_id) {
			// delete statement
			$builder = $this->getCommandBuilder();
			$criteria = new CDbCriteria();
			$criteria->addColumnCondition(array($tbl_keys[0] => $this->getPrimaryKey(), $tbl_keys[1] => $remove_id));
			$cmd = $builder->createDeleteCommand($tbl_name, $criteria);
			if (!$cmd->execute()) {
				throw new Exception("unable to delete removed many to many record for relation {$name} with pk {$remove_id}");
			}
		}
	}

	/**
	 * Saves related objects now that we have a pk for the instance
	 *
	 * @throws Exception
	 */
	protected function afterSave()
	{
		if ($this->auto_update_relations) {
			$record_relations = $this->getMetaData()->relations;
			// build a list of relations we need to update and the thru relations
			// that should be ignored (because the actual relations we're interested in will update these)
			$thru_rels = array();
			$many_rels = array();
			foreach ($record_relations as $name => $rel) {
				if (in_array(get_class($rel), array(self::HAS_MANY, self::MANY_MANY))) {
					$many_rels[] = $name;
					if ($rel->through) {
						$thru_rels[] = $rel->through;
					}
				}
			}
			foreach ($many_rels as $name) {
				if (in_array($name, $thru_rels) || !in_array($name, $this->safeAttributeNames)) {
					continue;
				}
				$rel = $record_relations[$name];
				$new_objs = $this->$name;
				$orig_objs = $this->getRelated($name, true);

				if (get_class($rel) == self::MANY_MANY) {
					foreach ($orig_objs as $obj) {
					}
					$this->afterSaveManyMany($name, $rel, $new_objs, $orig_objs);
				} else {
					if ($thru_name = $rel->through) {
						// This is a through relationship so need to update the assignment table
						$thru = $record_relations[$thru_name];
						if ($thru->className == $rel->className) {
							// same behaviour when the thru relation is the same class
							$this->afterSaveHasMany($name, $rel, $new_objs, $orig_objs);
						} else {
							$this->afterSaveThruHasMany($name, $rel, $thru, $new_objs);
						}
					}
					else {
						$this->afterSaveHasMany($name, $rel, $new_objs, $orig_objs);
					}
				}
			}
		}
		parent::afterSave();
	}

	/**
	 * Returns a date field in NHS format
	 *
	 * @param string $attribute
	 * @param string $empty_string - what to return if not able to convert
	 * @return string
	 */
	public function NHSDate($attribute, $empty_string = '-')
	{
		if ($value = $this->getAttribute($attribute)) {
			return Helper::convertMySQL2NHS($value, $empty_string);
		}
	}

	public function NHSDateAsHTML($attribute, $empty_string = '-')
	{
		if ($value = $this->getAttribute($attribute)) {
			return Helper::convertMySQL2HTML($value, $empty_string);
		}
	}

	public function audit($target, $action, $data=null, $log_message=null, $properties=array())
	{
		foreach (array('patient_id','episode_id','event_id','user_id','site_id','firm_id') as $field) {
			if (isset($this->{$field}) && !isset($properties[$field])) {
				$properties[$field] = $this->{$field};
			}
		}

		Audit::add($target, $action, $data, $log_message, $properties);
	}

	static public function cloneObject($object, $params=array()) {
		$class = get_class($object);

		$_object = new $class;

		foreach ($object as $key => $value) {
			if ($key != 'id') {
				$_object->{$key} = $value;
			}
		}

		foreach ($params as $key => $value) {
			$_object->{$key} = $value;
		}

		return $object;
	}

	/**
	 * Iterate through relations and remove the records that will break constraints
	 *
	 * @return bool
	 * @throws Exception
	 */

	protected function beforeDelete()
	{
		if ($this->auto_update_relations) {
			$deleted_classes = array();
			$record_relations = $this->getMetaData()->relations;
			foreach ($record_relations as $rel_name => $rel) {
				$rel_type = get_class($rel);
				if ($rel_type == self::MANY_MANY) {
					$tbl_name = $rel->getJunctionTableName();
					$tbl_keys = $rel->getJunctionForeignKeys();
					if (count($tbl_keys) == 2) {
						// if the relationship is more complex, this needs to be handled in the record class itself.
						$builder = $this->getCommandBuilder();
						$criteria = new CDbCriteria();
						$criteria->addColumnCondition(array($tbl_keys[0] => $this->getPrimaryKey()));
						$cmd = $builder->createDeleteCommand($tbl_name, $criteria);
						if (!$cmd->execute()) {
							throw new Exception("unable to delete entries for many many relation {$rel_name}");
						}
					}
				}
				elseif ($rel_type == self::HAS_MANY) {
					if (!$rel->through) {
						// if the relationship is 'through', then the delete is handled by that relationship so we ignore it
						$rel_cls = $rel->className;
						if (!in_array($rel_cls, $deleted_classes)){
							// only need to delete once for any given class as we are ignoring the conditions added to the relation
							// beyond the fk relation to this owning object (can't envision a relation based on a different fk relation
							// to the same model)
							$rel_cls::model()->deleteAllByAttributes(array($rel->foreignKey => $this->getPrimaryKey()));
							$deleted_classes[] = $rel_cls;
						}
					}
				}
			}
		}
		return parent::beforeDelete();
	}
}
