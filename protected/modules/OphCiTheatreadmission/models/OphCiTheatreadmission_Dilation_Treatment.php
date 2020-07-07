<?php

/**
 * This is the model class for table "ophcitheatreadmission_dilation_treatment".
 *
 * The followings are the available columns in table 'ophcitheatreadmission_dilation_treatment':
 * @property string $id
 * @property string $dilation_id
 * @property string $drug_id
 * @property string $drops
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property string $treatment_time
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property OEModule\OphCiExamination\models\OphCiExamination_Dilation_Drugs $drug
 * @property OphCiTheatreadmission_Dilation $element
 * @property User $lastModifiedUser
 */
class OphCiTheatreadmission_Dilation_Treatment extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphCiTheatreadmission_Dilation_Treatment the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophcitheatreadmission_dilation_treatment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('dilation_id, drug_id, drops, treatment_time', 'required'),
            array('dilation_id, drug_id, drops, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, dilation_id, drug_id, drops, last_modified_user_id, last_modified_date, created_user_id, created_date, treatment_time',
                'safe',
                'on' => 'search'
            ),
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
            'drug' => array(
                self::BELONGS_TO,
                'OEModule\OphCiExamination\models\OphCiExamination_Dilation_Drugs',
                'drug_id'
            ),
            'dilation' => array(self::BELONGS_TO, 'OphCiTheatreadmission_Dilation', 'dilation_id'),
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
            'dilation_id' => 'Element',
            'drug_id' => 'Drug',
            'drops' => 'Drops',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'treatment_time' => 'Treatment Time',
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

        $criteria->compare('id', $this->id, true);
        $criteria->compare('element_id', $this->dilation_id, true);
        $criteria->compare('drug_id', $this->drug_id, true);
        $criteria->compare('drops', $this->drops, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);
        $criteria->compare('treatment_time', $this->treatment_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
