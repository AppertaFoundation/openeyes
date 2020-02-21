<?php

/**
 * This is the model class for table "site_logo".
 *
 * The followings are the available columns in table 'site_logo':
 * @property integer $id
 * @property string $primary_logo
 * @property string $secondary_logo
 *
 * The followings are the available model relations:
 * @property Site[] $sites
 */
class SiteLogo extends BaseActiveRecord
{
    //public $primary_logo;
    //public $secondary_logo;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'site_logo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('primary_logo', 'file', 
                'types' => 'jpg, gif, png',
                'maxSize'=> 1024 * 1024 * 15, // 15MB
                'tooLarge'=>'The file was larger than 15MB. Please upload a smaller file.',
                'allowEmpty' => true,
                'safe' => false
            ),
            array('secondary_logo', 'file', 
                'types' => 'jpg, gif, png',
                'maxSize'=> 1024 * 1024 * 15, // 15MB
                'tooLarge'=>'The file was larger than 15MB. Please upload a smaller file.',
                'allowEmpty' => true,
                'safe' => false
            ),
            array('primary_logo, secondary_logo', 'safe'),
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
			'site' => array(self::HAS_MANY, 'Site', 'id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'primary_logo' => 'Primary Logo',
			'secondary_logo' => 'Secondary Logo',
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
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SiteLogo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
    }
    
    public function getImageUrl($logo_type=null)
    {
        $options = array('id' => $this->id);

        if ($logo_type) {
            $options['secondary_logo'] = $logo_type;
        }
        return Yii::app()->createUrl('//sitelogo/view', $options);
    }
}
