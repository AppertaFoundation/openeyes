<?php

/**
 * This is the model class for table "medication_merge".
 *
 * The followings are the available columns in table 'medication_merge':
 * @property integer $id
 * @property date $entry_date_time
 * @property integer $source_drug_id
 * @property integer $source_medication_id
 * @property string $source_code
 * @property string $source_name
 * @property integer $target_id
 * @property string $target_code
 * @property string $target_name
 * @property integer $status
 * @property date $merge_date
 *
 * The followings are the available model relations:
 * @property eventMedicationUse[] $eventMedicationUses
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Medication[] $medicationsSource
 * @property Medication[] $medicationsTarget
 */
class MedicationMerge extends BaseActiveRecord
{

	public static $ACTIVE_STATUS = 1;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'medication_merge';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('entry_date_time, source_drug_id, source_medication_id, source_code, source_name, target_id, target_code, target_name, status, merge_date', 'safe'),
			// The following rule is used by search().
			array('id, entry_date_time, source_drug_id, source_medication_id, source_code, source_name, target_id, target_code, target_name', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'eventMedicationUsesDrug' => array(self::HAS_MANY, EventMedicationUse::class, 'source_drug_id'),
			'eventMedicationUses' => array(self::HAS_MANY, EventMedicationUse::class, 'source_medication_id'),
			'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'medicationsSource' => array(self::MANY_MANY, Medication::class, array('source_medication_id' => 'id')),
			'medicationsTarget' => array(self::MANY_MANY, Medication::class, array('target_medication_id' => 'id')),
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MedicationRoute the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Return all entries where status = 1
	 * @return CActiveDataProvider
	 */
	private function getAllPending()
	{
		$criteria = new \CDbCriteria();
		$criteria->addCondition('status = ' . MedicationMerge::$ACTIVE_STATUS);
		return new CActiveDataProvider('medicationMerge', ['criteria' => $criteria,
			'pagination' => false]);
	}

	/*
	* Runs all pending merge at once
	*/
	public function mergeAll()
	{
		foreach ($this->getAllPending()->getData() as $merge_row) {
			// several cases need to be handled here:
			// 1. source_drug_id set / target_medication_id not set -> search by target_code
			// 2. source_medication_id set / target_medication_id set
			if (!$merge_row->source_medication_id || $merge_row->source_medication_id == '') {
				if ($merge_row->source_drug_id > 0) {
					$source_medication = Medication::model()->find("source_old_id = :old_id AND source_type='LOCAL' AND source_subtype='drug'", array(":old_id" => $merge_row->source_drug_id));
					$merge_row->source_medication_id = $source_medication->id;
				} else {
					Yii::log('Source medication cannot be found without a source drug id or a source medication id');
					continue;
				}
			} else {
				$source_medication = Medication::model()->findByPk($merge_row->source_medication_id);
			}

			if (!$merge_row->target_id || $merge_row->target_id == '') {
				$target_medication = Medication::model()->find("preferred_code = :national_code AND source_type='DM+D'", array(":national_code" => $merge_row->target_code));
				if ($target_medication) {
					$merge_row->target_id = $target_medication->id;
				} else {
					Yii::log('No target medication was found , cannot be merged');
					// we cannot merge without ID!
					continue;
				}
			}

			foreach (['event_medication_use', 'medication_allergy_assignment', 'medication_attribute_assignment', 'medication_common',
								 'medication_search_index', 'medication_set_auto_rule_medication', 'medication_set_item'] as $table_with_medication_id) {
				try {
					Yii::app()->db->createCommand("UPDATE {$table_with_medication_id} 
                                         SET medication_id = {$merge_row->target_id} 
                                         WHERE medication_id = {$merge_row->source_medication_id}")->execute();
				} catch (Exception $exception) {
					Yii::log("Cannot remap foreign keys for medication: " . $merge_row->target_code . "\n");
					Yii::log($exception->getMessage());
				}
			}
			$source_medication->deleted_date = date("Y-m-d");

			if (!$source_medication->save()) {
				Yii::log("Cannot deactivate medication: " . $source_medication->id . "\n");
			}

			$merge_row->merge_date = date("Y-m-d H:i:s");
			$merge_row->status = 0;
			if (!$merge_row->save()) {
				Yii::log("Cannot close merge " . $merge_row->id . "\n");
			}
		}
	}
}
