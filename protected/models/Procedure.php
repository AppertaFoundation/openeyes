<?php

/**
 * This is the model class for table "procedure".
 *
 * The followings are the available columns in table 'procedure':
 * @property string $id
 * @property string $term
 * @property string $short_format
 * @property integer $default_duration
 * @property string $service_subsection_id
 *
 * The followings are the available model relations:
 * @property ElementOperation[] $elementOperations
 * @property ServiceSubsection $serviceSubsection
 * @property OpcsCode[] $opcsCodes
 */
class Procedure extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Procedure the static model class
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
		return 'procedure';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('term, short_format, default_duration, service_subsection_id', 'required'),
			array('default_duration', 'numerical', 'integerOnly'=>true),
			array('term, short_format', 'length', 'max'=>255),
			array('service_subsection_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, term, short_format, default_duration, service_subsection_id', 'safe', 'on'=>'search'),
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
			'operations' => array(self::MANY_MANY, 'ElementOperation', 'operation_procedure_assignment(procedure_id, operation_id)'),
			'serviceSubsection' => array(self::BELONGS_TO, 'ServiceSubsection', 'service_subsection_id'),
			'opcsCodes' => array(self::MANY_MANY, 'OpcsCode', 'procedure_opcs_assignment(procedure_id, opcs_code_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'term' => 'Term',
			'short_format' => 'Short Format',
			'default_duration' => 'Default Duration',
			'service_subsection_id' => 'Service Subsection',
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
		$criteria->compare('term',$this->term,true);
		$criteria->compare('short_format',$this->short_format,true);
		$criteria->compare('default_duration',$this->default_duration);
		$criteria->compare('service_subsection_id',$this->service_subsection_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Get a list of procedures
	 * Store extra data for the session
	 * 
	 * @param string  $term          term to search by
	 * 
	 * @return array
	 */
	public static function getList($term)
	{
		$search = "{$term}%";
		
		$select = 'term, short_format, id, default_duration';
		
		$procedures = Yii::app()->db->createCommand()
			->select($select)
			->from('procedure')
			->where('term LIKE :term OR short_format LIKE :format', 
				array(':term'=>$search, ':format'=>$search))
			->queryAll();

		$data = array();
		$session = Yii::app()->session['Procedures'];

		foreach ($procedures as $procedure) {
			$data[] = "{$procedure['term']} - {$procedure['short_format']}";
			$id = $procedure['id'];
			$session[$id] = array(
				'term' => $procedure['term'],
				'short_format' => $procedure['short_format'],
				'duration' => $procedure['default_duration'],
			);
		}
		
		Yii::app()->session['Procedures'] = $session;
		
		return $data;
	}
}