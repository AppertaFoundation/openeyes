<?php

/**
 * This is the model class for table "ophtroperationnote_attribute".
 *
 * The followings are the available columns in table 'ophtroperationnote_attribute':
 * @property integer $id
 * @property string $name
 * @property string $label
 * @property integer $display_order
 * @property string $proc_id
 * @property integer $is_multiselect
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Procedure $procedure
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Ophtroperationnote_AttributeOption[] $ophtroperationnoteAttributeOptions
 */
class OphTrOperationnote_Attribute extends BaseActiveRecordVersioned
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ophtroperationnote_attribute';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, label, display_order, proc_id', 'required'),
			array('display_order, is_multiselect', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>40),
			array('label', 'length', 'max'=>255),
			array('proc_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('last_modified_date, created_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, label, display_order, proc_id, is_multiselect, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
			'procedure' => array(self::BELONGS_TO, Procedure::class, 'proc_id'),
			'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'ophtroperationnoteAttributeOptions' => array(self::HAS_MANY, 'OphtroperationnoteAttributeOption', 'attribute_id'),
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
			'label' => 'Label',
			'display_order' => 'Display Order',
			'proc_id' => 'Proc',
			'is_multiselect' => 'Is Multiselect',
			'last_modified_user_id' => 'Last Modified User',
			'last_modified_date' => 'Last Modified Date',
			'created_user_id' => 'Created User',
			'created_date' => 'Created Date',
			'procedure.term' => 'Procedure',
			'getItemsAdminLink' => 'Action',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('label',$this->label,true);
		$criteria->compare('display_order',$this->display_order);
		$criteria->compare('proc_id',$this->proc_id,true);
		$criteria->compare('is_multiselect',$this->is_multiselect);
		$criteria->compare('last_modified_user_id',$this->last_modified_user_id,true);
		$criteria->compare('last_modified_date',$this->last_modified_date,true);
		$criteria->compare('created_user_id',$this->created_user_id,true);
		$criteria->compare('created_date',$this->created_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OphTrOperationnoteAttribute the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function beforeValidate()
	{
		if(is_null($this->display_order)) {
			if($last = self::model()->findBySql("SELECT * FROM ".$this->tableName()." ORDER BY `display_order` DESC LIMIT 1")) {
				$this->display_order = $last->display_order + 1;
			}
			else {
				$this->display_order = 1;
			}
		}

		return parent::beforeValidate();
	}

	public function afterFind()
	{
		$this->is_multiselect = (bool)$this->is_multiselect;
	}

	public function getItemsAdminLink()
	{
		return '<a href="/OphTrOperationnote/attributeOptionsAdmin/index?attribute_id='.$this->id.'">Manage items</a>';
	}
}
