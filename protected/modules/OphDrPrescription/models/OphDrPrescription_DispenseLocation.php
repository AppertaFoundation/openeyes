<?php

use OE\factories\models\traits\HasFactory;

/**
 * Created by PhpStorm.
 * User: veta
 * Date: 19/08/2017
 * Time: 23:23
 */
class OphDrPrescription_DispenseLocation extends BaseActiveRecordVersioned
{
    use MappedReferenceData;
    use HasFactory;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'dispense_location_id';
    }

    protected function mappingModelName(int $level): string
    {
        return 'OphDrPrescription_DispenseLocation_Institution';
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrprescription_dispense_location';
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
            array('created_date, name, display_order, created_user_id, last_modified_user_id, last_modified_date,', 'safe'),
            array('id, caption', 'safe', 'on'=>'search'),
        );
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'dispense_location_institutions' => [self::HAS_MANY, 'OphDrPrescription_DispenseLocation_Institution', 'dispense_location_id'],
            'institutions' => [self::MANY_MANY, 'Institution', 'ophdrprescription_dispense_location_institution(dispense_location_id, institution_id)']
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

    public function defaultScope()
    {
        return ['order' => 'display_order'];
    }

    private static function getDispenseLocationsForInstitution(int $institution_id)
    {
        return OphDrPrescription_DispenseLocation_Institution::model()->findAll("institution_id=:institution_id", [":institution_id" => $institution_id]);
    }

    /**
     * Returns the Dispense Conditions that have the location assigned
     *
     * @param Institution $institution
     * @return array
     */
    public static function getDispenseConditionAssignmentsForInstitution(int $institution_id): array
    {
        $dispense_location_institution = self::getDispenseLocationsForInstitution($institution_id);

        $dispense_conditions = [];
        foreach ($dispense_location_institution as $dispense_location_institution) {
            foreach ($dispense_location_institution->dispense_condition_institutions as $dispense_condition_institution) {
                $dispense_conditions[$dispense_location_institution->dispense_location->id][] = $dispense_condition_institution->dispense_condition;
            }
        }

        return $dispense_conditions;
    }
}
