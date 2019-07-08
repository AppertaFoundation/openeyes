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
 * @property eventMedicationUsesDrug[] $eventMedicationUses
 * @property eventMedicationUses[] $eventMedicationUses
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Medication[] $medicationsSource
 * @property Medication[] $medicationsTarget
 */
class MedicationMerge extends BaseActiveRecord
{
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
			// @todo Please remove those attributes that should not be searched.
			array('id, entry_date_time, source_drug_id, source_medication_id, source_code, source_name, target_id, target_code, target_name', 'safe', 'on'=>'search'),
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
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /*
    * Return all entries where status = 1
    */

    private function getAllPending()
    {
        return $this->findAllByAttributes(array('status'=>1));
    }

    /*
    * Runs all pending merge at once
    */
    public function mergeAll()
    {
        foreach($this->getAllPending() as $merge_row)
        {
            // several cases need to be handled here:
            // 1. source_drug_id set / target_medication_id not set -> search by target_code
            // 2. source_medication_id set / target_medication_id set
            
        }
    }
}
