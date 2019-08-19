<?php

/**
 * This is the model class for table "import".
 *
 * The followings are the available columns in table 'import':
 * @property integer $id
 * @property integer $parent_log_id
 * @property string $message
 * @property int import_status_id
 */
class Import extends BaseActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'import';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_log_id, message, import_status_id', 'required'),
			array('parent_log_id', 'numerical', 'integerOnly'=>true),
			array('message', 'length', 'max'=>255),
			array('import_status_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, parent_log_id, message, import_status_id', 'safe', 'on'=>'search'),
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
			'parent_log' => array(self::BELONGS_TO, 'ImportLog', 'parent_log_id'),
			'import_status' => array(self::BELONGS_TO, 'ImportStatus', 'import_status_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'parent_log_id' => 'Parent Log',
			'message' => 'Message',
			'import_status_id' => 'Import Status',
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
		$criteria->compare('parent_log_id',$this->parent_log_id);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Import the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
