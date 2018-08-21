<?php

/**
 * This is the model class for table "treatment_type".
 *
 * The followings are the available columns in table 'treatment_type':
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property TrialPatient[] $trialPatients
 * @property User $lastModifiedUser
 * @property User $createdUser
 */
class TreatmentType extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'treatment_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, code', 'required'),
            array('name, code', 'length', 'max' => 64),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'trialPatients' => array(self::HAS_MANY, 'TrialPatient', 'treatment_type_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TreatmentType the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
