<?php

/**
 * This is the model class for table "import_log".
 *
 * The followings are the available columns in table 'import_log':
 * @property integer $id
 * @property string $startdatetime
 * @property string $enddatetime
 * @property string $status
 * @property integer $import_user_id
 */
class ImportLog extends BaseActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'import_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('startdatetime, status, import_user_id', 'required'),
			array('import_user_id', 'numerical', 'integerOnly'=>true),
			//array('status', 'length', 'max'=>255),
			array('enddatetime', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, startdatetime, enddatetime, status, import_user_id', 'safe', 'on'=>'search'),
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
			'imports' => array(self::HAS_MANY, 'Import', 'parent_log_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'startdatetime' => 'Startdatetime',
			'enddatetime' => 'Enddatetime',
			'status' => 'Status',
			'import_user_id' => 'Import User',
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
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('startdatetime',$this->startdatetime,true);
		$criteria->compare('enddatetime',$this->enddatetime,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('import_user_id',$this->import_user_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
