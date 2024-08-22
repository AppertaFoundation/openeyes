<?php

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "trial_patient_status".
 *
 * The followings are the available columns in table 'trial_patient_status':
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $display_order
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
class TrialPatientStatus extends BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * The status when the patient has been just added to a Trial, but hasn't been accepted or rejected yet
     */
    const SHORTLISTED_CODE = 'SHORTLISTED';

    /**
     * The status when the patient has been accepted into the Trial
     */
    const ACCEPTED_CODE = 'ACCEPTED';

    /**
     * The status when the patient hsa been rejected from the Trial
     */
    const REJECTED_CODE = 'REJECTED';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'trial_patient_status';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, code, display_order', 'required'),
            array('display_order', 'numerical', 'integerOnly' => true),
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
            'trialPatients' => array(self::HAS_MANY, 'TrialPatient', 'status_id'),
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
            'display_order' => 'Display Order',
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
     * @return TrialPatientStatus the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
