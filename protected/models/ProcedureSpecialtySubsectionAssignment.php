<?php

/**
 * This is the model class for table "proc_specialty_subsection_assignment".
 *
 * The followings are the available columns in table 'proc_specialty_subsection_assignment':
 * @property string $id
 * @property string $proc_id
 * @property string $specialty_subsection_id
 *
 * The followings are the available model relations:
 * @property SpecialtySubsection $specialtySubsection
 * @property Proc $proc
 */
class ProcedureSpecialtySubsectionAssignment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ProcedureSpecialtySubsectionAssignment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'proc_specialty_subsection_assignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('proc_id, specialty_subsection_id', 'required'),
			array('proc_id, specialty_subsection_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, proc_id, specialty_subsection_id', 'safe', 'on'=>'search'),
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
			'specialtySubsection' => array(self::BELONGS_TO, 'SpecialtySubsection', 'specialty_subsection_id'),
			'proc' => array(self::BELONGS_TO, 'Proc', 'proc_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'proc_id' => 'Proc',
			'specialty_subsection_id' => 'Specialty Subsection',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('proc_id',$this->proc_id,true);
		$criteria->compare('specialty_subsection_id',$this->specialty_subsection_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}