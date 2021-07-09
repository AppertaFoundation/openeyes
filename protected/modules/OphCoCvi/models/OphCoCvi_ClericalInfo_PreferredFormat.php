<?php

/**
 * This is the model class for table "ophcocvi_clericinfo_preferred_format".
 *
 * The followings are the available columns in table 'ophcocvi_clericinfo_preferred_format':
 * @property integer $id
 * @property string $name
 * @property integer $describe_needs
 * @property string $display_order
 * @property integer $active
 * @property string $version
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property EtOphcocviClericinfo[] $etOphcocviClericinfos
 * @property User $createdUser
 * @property User $lastModifiedUser
 */

namespace OEModule\OphCoCvi\models;

class OphCoCvi_ClericalInfo_PreferredFormat extends \BaseEventTypeElement
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ophcocvi_clericinfo_preferred_format';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('describe_needs, active', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>128),
			array('display_order, version, last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('last_modified_date, created_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, describe_needs, display_order, active, version, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
                'clerical_info' => array(self::HAS_MANY, 'EtOphcocviClericinfo', 'preferred_format_id'),
                'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
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
                'describe_needs' => 'Describe Needs',
                'display_order' => 'Display Order',
                'active' => 'Active',
                'version' => 'Version',
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
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('describe_needs',$this->describe_needs);
		$criteria->compare('display_order',$this->display_order,true);
		$criteria->compare('active',$this->active);
		$criteria->compare('version',$this->version,true);
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
	 * @return OphCoCvi_ClericInfo_PreferredFormat the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
