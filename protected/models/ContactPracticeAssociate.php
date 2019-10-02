<?php

/**
 * This is the model class for table "contact_practice_associate".
 *
 * The followings are the available columns in table 'contact_practice_associate':
 * @property integer $id
 * @property string $gp_id
 * @property string $practice_id
 * @property string $provider_no
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property Gp $gp
 * @property User $lastModifiedUser
 * @property Practice $practice
 */
class ContactPracticeAssociate extends BaseActiveRecordVersioned
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'contact_practice_associate';
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
			array('provider_no', 'unique', 'message'=>'Duplicate provider number.'),
			array('gp_id, practice_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
			array('last_modified_date, created_date', 'safe'),
			// The following rule is used by search().
			array('id, gp_id, practice_id, provider_no', 'safe', 'on'=>'search'),
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
			'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'practice' => array(self::BELONGS_TO, 'Practice', 'practice_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'gp_id' => 'Gp',
			'practice_id' => 'Practice',
            'provider_no' => 'Provider number',
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
		$criteria->compare('gp_id',$this->gp_id,true);
		$criteria->compare('practice_id',$this->practice_id,true);
        $criteria->compare('provider_no',$this->provider_no,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ContactPracticeAssociate the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getLetterAddress(){

	    $contact = $this->practice->contact;

	    $address = $contact->address;

        return $address->getLetterArray();
    }
}
