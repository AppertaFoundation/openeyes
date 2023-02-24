<?php

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "trial_type".
 *
 * The followings are the available columns in table 'trial_type':
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $lastModifiedUser
 * @property User $createdUser
 */
class TrialType extends BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * The trial type for non-Intervention trial (meaning there are no restrictions on assigning patients to this the trial)
     */
    const NON_INTERVENTION_CODE = 'NON_INTERVENTION';

    /**
     * The trial type for Intervention trials (meaning a patient can only be assigned to one ongoing Intervention trial at a time)
     */
    const INTERVENTION_CODE = 'INTERVENTION';


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'trial_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, code', 'required'),
            array('name, code', 'length', 'max' => 64),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array(
                'id, name, code, last_modified_user_id, last_modified_date, created_user_id, created_date',
                'safe',
                'on' => 'search',
            ),
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
     * Gets all trial type options as 'id' => 'name' (for use in dropdown lists)
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
     * @return TrialType the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
