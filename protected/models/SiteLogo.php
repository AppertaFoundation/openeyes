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
            array('primary_logo, secondary_logo, parent_logo', 'safe'),
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
            'institution' => array(self::HAS_MANY, 'Institution', 'id'),
            'parent' => array(self::HAS_ONE, 'SiteLogo' , 'id'),
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
     * @return string The URL for the logo
     */
    public function getImageUrl($logo_type = null)
    {
        $options = array('id' => $this->id);

        if ($logo_type) {
            $options['secondary_logo'] = $logo_type;
        }
        return Yii::app()->createUrl('sitelogo/view', $options);
    }
}
