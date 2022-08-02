<?php

/**
 * This is the model class for table "postcode_to_lsoa_mapping".
 *
 * The followings are the available columns in table 'postcode_to_lsoa_mapping':
 * @property integer $id
 * @property string $postcode
 * @property string $lsoa
 */
class PostcodeToLSOAMapping extends BaseActiveRecord
{
    public $pre_purified_skip_purification_in_validation = ['postcode', 'lsoa'];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'postcode_to_lsoa_mapping';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('postcode', 'length', 'max'=>7),
            array('lsoa', 'length', 'max'=>9),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, postcode, lsoa', 'safe', 'on'=>'search'),
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
            'postcode' => 'Postcode',
            'lsoa' => 'Lsoa',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('postcode', $this->postcode, true);
        $criteria->compare('lsoa', $this->lsoa, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PostcodeToLsoaMapping the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
