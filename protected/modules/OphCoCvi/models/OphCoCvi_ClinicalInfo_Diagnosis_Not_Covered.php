<?php
namespace OEModule\OphCoCvi\models;
/**
 * This is the model class for table "et_ophcocvi_clinicinfo_diagnosis_not_covered".
 *
 * The followings are the available columns in table 'et_ophcocvi_clinicinfo_diagnosis_not_covered':
 * @property integer $id
 * @property string $element_id
 * @property string $disorder_id
 * @property integer $eye_id
 * @property integer $main_cause
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered extends \BaseActiveRecordVersioned
{

    const TYPE_CLINICINFO_DISORDER = 1;  //table -> ophcocvi_et_clinicinfo_disorder
    const TYPE_DISORDER = 2; //table -> disorder
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'et_ophcocvi_clinicinfo_diagnosis_not_covered';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('eye_id, main_cause', 'numerical', 'integerOnly'=>true),
			array('element_id, disorder_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('last_modified_date, created_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, element_id, disorder_id, eye_id, main_cause, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'element' => array(self::BELONGS_TO, 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_V1', 'element_id'),
            'clinicinfo_disorder' => array(
                self::BELONGS_TO,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder',
                'disorder_id'
            ),
            'disorder' => array(
                self::BELONGS_TO,
                'Disorder',
                'disorder_id'
            ),
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
			'element_id' => 'Element',
			'disorder_id' => 'Disorder',
			'eye_id' => 'Eye',
			'main_cause' => 'Main Cause',
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
		$criteria->compare('element_id',$this->element_id,true);
		$criteria->compare('disorder_id',$this->disorder_id,true);
		$criteria->compare('eye_id',$this->eye_id);
		$criteria->compare('main_cause',$this->main_cause);
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
	 * @return OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
