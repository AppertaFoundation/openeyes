<?php

/**
 * This is the model class for table "patient_merge_request".
 *
 * The followings are the available columns in table 'patient_merge_request':
 *
 * @property int $id
 * @property string $primary_id
 * @property string $primary_local_identifier_value
 * @property string $primary_global_identifier_value
 * @property string $primary_dob
 * @property string $primary_gender
 * @property string $secondary_id
 * @property string $secondary_local_identifier_value
 * @property string $secondary_global_identifier_value
 * @property string $secondary_dob
 * @property string $secondary_gender
 * @property string $merge_json
 * @property string $comment
 * @property int $status
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Patient $secondaryPatient
 * @property User $createdUser
 * @property Patient $primaryPatient
 * @property User $lastModifiedUser
 */
class PatientMergeRequest extends BaseActiveRecordVersioned
{
    const STATUS_NOT_PROCESSED = 0;
    const STATUS_CONFLICT = 10;
    const STATUS_MERGED = 20;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'patient_merge_request';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                    array('primary_id, secondary_id', 'required'),
                    array('status', 'numerical', 'integerOnly' => true),
                    array('primary_id, secondary_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
                    array('primary_local_identifier_value, primary_global_identifier_value, secondary_local_identifier_value, secondary_global_identifier_value', 'length', 'max' => 40),
                    array('primary_gender, secondary_gender', 'length', 'max' => 1),
                    array('primary_dob, secondary_dob, merge_json, comment, last_modified_date, created_date', 'safe'),
                    // The following rule is used by search().
                    // @todo Please remove those attributes that should not be searched.
                    array('id, primary_id, primary_local_identifier_value, primary_global_identifier_value, primary_dob, primary_gender, secondary_id, secondary_local_identifier_value, secondary_global_identifier_value, secondary_dob, secondary_gender, merge_json, comment, status, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on' => 'search'),
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
            'primaryPatient' => array(self::BELONGS_TO, 'Patient', 'primary_id'),
            'secondaryPatient' => array(self::BELONGS_TO, 'Patient', 'secondary_id'),

        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                    'id' => 'ID',
                    'primary_id' => 'Primary',
                    'primary_local_identifier_value' => 'Primary Local Identifier',
                    'primary_global_identifier_value' => 'Primary global_identifier_value',
                    'primary_dob' => 'Primary Dob',
                    'primary_gender' => 'Primary Sex',
                    'secondary_id' => 'Secondary',
                    'secondary_local_identifier_value' => 'Secondary Local Identifier',
                    'secondary_global_identifier_value' => 'secondary global_identifier_value',
                    'secondary_dob' => 'Secondary Dob',
                    'secondary_gender' => 'secondary Sex',
                    'merge_json' => 'Merge Json',
                    'comment' => 'Comment',
                    'status' => 'Status',
                    'last_modified_user_id' => 'Last Modified User',
                    'last_modified_date' => 'Last Modified Date',
                    'created_user_id' => 'Created User',
                    'created_date' => 'Created Date',
            );
    }

    public function behaviors()
    {
        return array(
                    'OeDateFormat' => array(
                        'class' => 'application.behaviors.OeDateFormat',
                        'date_columns' => array('primary_dob', 'secondary_dob', 'last_modified_date', 'created_date'),
                    ),
            );
    }

    public function beforeSave()
    {
        $this->primary_gender = $this->primary_gender;

        return parent::beforeSave();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     *                             based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

            $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('primary_id', $this->primary_id, true);
        $criteria->compare('primary_local_identifier_value', $this->primary_local_identifier_value, true);
        $criteria->compare('primary_global_identifier_value', $this->primary_global_identifier_value, true);
        $criteria->compare('primary_dob', $this->primary_dob, true);
        $criteria->compare('primary_gender', $this->primary_gender, true);
        $criteria->compare('secondary_id', $this->secondary_id, true);
        $criteria->compare('secondary_local_identifier_value', $this->secondary_local_identifier_value, true);
        $criteria->compare('secondary_global_identifier_value', $this->secondary_global_identifier_value, true);
        $criteria->compare('secondary_dob', $this->secondary_dob, true);
        $criteria->compare('secondary_gender', $this->secondary_gender, true);
        $criteria->compare('merge_json', $this->merge_json, true);
        $criteria->compare('comment', $this->comment, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
            ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className active record class name.
     *
     * @return PatientMergeRequest the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getStatusText()
    {
        $text = '';

        switch ($this->status) {
            case self::STATUS_NOT_PROCESSED:
                $text = 'Not processed';
                break;
            case self::STATUS_CONFLICT:
                $text = 'Conflict';
                break;
            case self::STATUS_MERGED:
                $text = 'Merged';
                break;
            default:
                $text = 'Unknown';
        }

        return $text;
    }

    public function getMergedMessage()
    {
        return "Identifier <strong>({$this->secondary_local_identifier_value}) {$this->secondaryPatient->getFullName()}</strong> was merged into <strong>({$this->primary_local_identifier_value}) {$this->primaryPatient->getFullName()}</strong> on {$this->created_date}";
    }
}
