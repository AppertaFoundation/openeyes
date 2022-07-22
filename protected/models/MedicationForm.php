<?php

/**
 * This is the model class for table "medication_form".
 *
 * The followings are the available columns in table 'medication_form':
 * @property integer $id
 * @property string $term
 * @property string $code
 * @property string $unit_term
 * @property string $default_dose_unit_term
 * @property string $source_type
 * @property string $source_subtype
 * @property string $deleted_date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $lastModifiedUser
 * @property User $createdUser
 * @property MedicationSetItem[] $medicationSetItems
 */
class MedicationForm extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication_form';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('term, code, unit_term, default_dose_unit_term, source_type, source_subtype', 'length', 'max'=>45),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('deleted_date, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, term, code, unit_term, default_dose_unit_term, source_type, source_subtype, deleted_date, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'medicationSetItems' => array(self::HAS_MANY, MedicationSetItem::class, 'default_form_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'term' => 'Term',
            'code' => 'Code',
            'unit_term' => 'Unit Term',
            'default_dose_unit_term' => 'Default Dose Unit Term',
            'source_type' => 'Source Type',
            'source_subtype' => 'Source Subtype',
            'deleted_date' => 'Deleted Date',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('term', $this->term, true);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('unit_term', $this->unit_term, true);
        $criteria->compare('default_dose_unit_term', $this->default_dose_unit_term, true);
        $criteria->compare('source_type', $this->source_type, true);
        $criteria->compare('source_subtype', $this->source_subtype, true);
        $criteria->compare('deleted_date', $this->deleted_date, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MedicationForm the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
