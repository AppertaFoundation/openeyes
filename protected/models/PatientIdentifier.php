<?php

/**
 * This is the model class for table "patient_identifier".
 *
 * The followings are the available columns in table 'patient_identifier':
 * @property integer $id
 * @property string $patient_id
 * @property string code
 * @property string $value
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $lastModifiedUser
 * @property User $createdUser
 */
class PatientIdentifier extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'patient_identifier';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('patient_id, code', 'required'),
            array('patient_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('code', 'length', 'max' => 50),
            array('value', 'length', 'max' => 255),
            array('last_modified_date, created_date', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
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
            'patient_id' => 'Patient',
            'code' => 'Code',
            'value' => 'Value',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    public function getLabel()
    {
        if (isset(Yii::app()->params['patient_identifiers'])) {
            foreach (Yii::app()->params['patient_identifiers'] as $identifier_config) {
                if ($identifier_config['code'] === $this->code) {
                    return $identifier_config['label'];
                }
            }
        }
        return ucwords(strtolower(str_replace('_', ' ', $this->code)));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PatientIdentifier the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
