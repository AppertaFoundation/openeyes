<?php

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "treatment_type".
 *
 * The followings are the available columns in table 'treatment_type':
 * @property int $id
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
    use HasFactory;

    /**
     * The treatment type when users don't know whether the patient had intervention treatment or not (also the default value)
     */
    const UNKNOWN_CODE = 'UNKNOWN';
    /**
     * The treatment type when it is known that the patient had intervention surgery or medication
     */
    const INTERVENTION_CODE = 'INTERVENTION';
    /**
     * The treatment type when the patient had a placebo instead of intervention surgery or medicine
     */
    const PLACEBO_CODE = 'PLACEBO';


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
     * Gets all treatment type options as 'id' => 'name' (for use in dropdown lists)
     *
     * @return array
     */
    public static function getOptions()
    {
        return CHtml::listData(self::model()->findAll(), 'id', 'name');
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TreatmentType|BaseActiveRecord the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
