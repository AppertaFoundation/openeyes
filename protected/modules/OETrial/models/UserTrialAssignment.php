<?php

/**
 * This is the model class for table "user_trial_assignment".
 *
 * The followings are the available columns in table 'user_trial_assignment':
 * @property int $id
 * @property int $user_id
 * @property int $trial_id
 * @property int $trial_permission_id
 * @property string $role
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $user
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Trial $trial
 * @property TrialPermission $trialPermission
 */
class UserTrialAssignment extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user_trial_assignment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('user_id, trial_id, trial_permission_id', 'required'),
            array('trial_id', 'numerical', 'integerOnly' => true),
            array('user_id, trial_permission_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('role', 'length', 'max' => 255),
            array('last_modified_date, created_date', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'trial' => array(self::BELONGS_TO, 'Trial', 'trial_id'),
            'trialPermission' => array(self::BELONGS_TO, 'TrialPermission', 'trial_permission_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'user_id' => 'User',
            'trial_id' => 'Trial',
            'permission' => 'Permission',
            'role' => 'Role',
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
     * @return UserTrialAssignment the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
