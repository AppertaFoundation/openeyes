<?php

/**
 * This is the model class for table "patient_contact_associate".
 *
 * The followings are the available columns in table 'patient_contact_associate':
 * @property integer $id
 * @property string $patient_id
 * @property string $gp_id
 * @property string $practice_id
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property Gp $gp
 * @property Practice $practice
 * @property User $lastModifiedUser
 * @property Patient $patient
 */
class PatientContactAssociate extends BaseActiveRecordVersioned
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'patient_contact_associate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gp_id, practice_id', 'required'),
			array('patient_id, gp_id, practice_id,last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('last_modified_date, created_date', 'safe'),
			// The following rule is used by search().
			array('id, patient_id, gp_id', 'safe', 'on'=>'search'),
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
			'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'gp' => array(self::BELONGS_TO, 'Gp', 'gp_id'),
            'practice' => array(self::BELONGS_TO, 'Practice', 'practice_id'),
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
			'gp_id' => 'Gp',
            'practice_id' => 'Practice',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('patient_id',$this->patient_id,true);
		$criteria->compare('gp_id',$this->gp_id,true);
		$criteria->compare('practice_id',$this->practice_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PatientContactAssociate the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getGpsByPatientId($patient_id){
	    $return_array = array();
	    $command = Yii::app()->db->createCommand()->select('gp_id')->from('patient_contact_associate')->where('patient_id = '.$patient_id);
	    $records = $command->query();
	    foreach ($records as $record ){
	        $return_array[] = $record['gp_id'];
        }
	    return $return_array;
    }
}
