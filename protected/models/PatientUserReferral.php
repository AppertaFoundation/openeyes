<?php

/**
 * This is the model class for table "patient_user_referral".
 *
 * The followings are the available columns in table 'patient_user_referral':
 * @property integer $id
 * @property string $patient_id
 * @property string $user_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $user
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Patient $patient
 */
class PatientUserReferral extends BaseActiveRecord
{
  /**
   * @return string the associated database table name
   */
  public function tableName()
  {
    return 'patient_user_referral';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('patient_id', 'required'),
      array('patient_id, user_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
      array('last_modified_date, created_date', 'safe'),
      // The following rule is used by search().
      array(
        'id, patient_id, user_id',
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
      'user' => array(self::BELONGS_TO, 'User', 'user_id'),
      'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
      'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
      'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
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
      'user_id' => 'User',
      'last_modified_user_id' => 'Last Modified User',
      'last_modified_date' => 'Last Modified Date',
      'created_user_id' => 'Created User',
      'created_date' => 'Created Date',
    );
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
   * based on the search/filter conditions.
   */
  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('id', $this->id);
    $criteria->compare('patient_id', $this->patient_id, true);
    $criteria->compare('user_id', $this->user_id, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }

  public function getUserName()
  {
    return $this->user->getFullName();
  }
}
