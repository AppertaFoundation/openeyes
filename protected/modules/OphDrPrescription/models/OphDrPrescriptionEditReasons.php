<?php

/**
 * This is the model class for table "ophdrprescription_edit_reasons".
 *
 * The followings are the available columns in table 'ophdrprescription_edit_reasons':
 * @property integer $id
 * @property string $caption
 * @property integer $display_order
 * @property string $created_date
 * @property string $created_user_id
 */
class OphDrPrescriptionEditReasons extends BaseActiveRecord
{
    use MappedReferenceData;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return $this->tableName().'_id';
    }

    const SELECTION_LABEL_FIELD = 'caption';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrprescription_edit_reasons';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('caption, active', 'required'),
            array('display_order', 'numerical', 'integerOnly'=>true),
            array('caption', 'length', 'max'=>255),
            array('created_user_id', 'length', 'max'=>10),
            array('created_date', 'safe'),
            array('id, caption, active', 'safe', 'on'=>'search'),
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
            'et_ophdrprescription_details'=>array(self::BELONGS_TO, 'Element_OphDrPrescription_Details', 'edit_reason_id'),
            'institutions' => array(self::MANY_MANY, 'Institution', $this->tableName().'_institution('.$this->tableName().'_id, institution_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'caption' => 'Caption',
            'display_order' => 'Display Order',
            'created_date' => 'Created Date',
            'created_user_id' => 'Created By',
            'active' => 'Active'
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

        $criteria->compare('id', $this->id);
        $criteria->compare('caption', $this->caption, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphdrprescriptionEditReasons the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
