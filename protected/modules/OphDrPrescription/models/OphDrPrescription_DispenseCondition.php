<?php

/**
 * Created by PhpStorm.
 * User: veta
 * Date: 19/08/2017
 * Time: 23:23
 */
class OphDrPrescription_DispenseCondition extends BaseActiveRecordVersioned
{
    use MappedReferenceData;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'dispense_condition_id';
    }

    protected function mappingModelName(int $level): string
    {
        return 'OphDrPrescription_DispenseCondition_Institution';
    }

    protected $auto_update_relations = true;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrprescription_dispense_condition';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, display_order', 'required'),
            array('display_order', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>255),
            array('created_user_id', 'length', 'max'=>10),
            array('created_date, name, display_order, created_user_id, last_modified_user_id, last_modified_date, locations, all_locations', 'safe'),
            array('id, caption', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'dispense_condition_institutions' => [self::HAS_MANY, 'OphDrPrescription_DispenseCondition_Institution', 'dispense_condition_id'],
            'institutions' => [self::MANY_MANY, 'Institution', 'ophdrprescription_dispense_condition_institution(dispense_condition_id, institution_id)']
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'display_order' => 'Display Order',
            'created_date' => 'Created Date',
            'created_user_id' => 'Created By',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return ['order' => 'display_order'];
    }

    public function getLocationsForCurrentInstitution()
    {
        $locations = array();
        $dc_institution = OphDrPrescription_DispenseCondition_Institution::model()->findByAttributes(
            [
                'institution_id' => Yii::app()->session['selected_institution_id'],
                'dispense_condition_id' => $this->id
            ]
        );
        foreach ($dc_institution->dispense_location_institutions as $dl_institution) {
            $locations[] = $dl_institution->dispense_location;
        }
        return $locations;
    }
}
