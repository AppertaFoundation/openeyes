<?php

/**
 * This is the model class for table "referral_episode_assignment".
 *
 * The followings are the available columns in table 'referral_episode_assignment':
 * @property string $id
 * @property string $referral_id
 * @property string $episode_id
 */
class ReferralEpisodeAssignment extends MultiActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ReferralEpisodeAssignment the static model class
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
		return 'referral_episode_assignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('referral_id, episode_id', 'required'),
			array('referral_id, episode_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, referral_id, episode_id', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'referral_id' => 'Referral',
			'episode_id' => 'Episode',
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
		$criteria->compare('referral_id',$this->referral_id,true);
		$criteria->compare('episode_id',$this->episode_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}