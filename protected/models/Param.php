<?php

/**
 * This is the model class for table "param".
 *
 * The followings are the available columns in table 'param':
 * @property integer $id
 * @property string $param_key
 * @property string $param_value
 */
class Param extends BaseActiveRecord
{
	protected $editableParams = array(
		'watermark' => 'User Banner',
		'watermark_admin' => 'Admin Banner',
		'helpdesk_email' => 'Helpdesk Email',
		'helpdesk_phone' => 'Helpdesk Telephone',
		'alerts_email' => 'Alerts Email',
		'adminEmail' => 'Admin Email'
	);

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'param';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('param_key, param_value', 'required'),
			array('id', 'numerical', 'integerOnly' => true),
			array('param_key', 'isEditableParam'),
			array('param_key, param_value', 'length', 'max' => 255),
			// The following rule is used by search().
			array('param_key, param_value', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'param_key' => 'Key',
			'param_value' => $this->getEditableParamCleanName($this->param_key),
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

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('param_key', $this->param_key, true);
		$criteria->compare('param_value', $this->param_value, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Param the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return array
	 */
	public function getEditableParams()
	{
		return $this->editableParams;
	}

	/**
	 * @param array $editableParams
	 */
	public function setEditableParams($editableParams)
	{
		$this->editableParams = $editableParams;
	}

	/**
	 * @param $attribute
	 * @param $params
	 */
	public function isEditableParam($attribute, $params)
	{
		if(!in_array($this->param_key, array_keys($this->editableParams))){
			$this->addError($attribute, 'The key used is not an editable param.');
		}
	}

	/**
	 * @return array
	 */
	public function findAllKeyValue()
	{
		$params = $this->findAll();
		$keyValue = array();
		foreach($params as $param){
			$keyValue[$param->param_key] = $param->param_value;
		}

		return $keyValue;
	}

	/**
	 * @param $param
	 * @return string
	 */
	public function getEditableParamCleanName($param)
	{
		if(array_key_exists($param, $this->editableParams) && !empty($this->editableParams[$param])){
			return $this->editableParams[$param];
		}

		return $this->getAttributeLabel($param);
	}
}
