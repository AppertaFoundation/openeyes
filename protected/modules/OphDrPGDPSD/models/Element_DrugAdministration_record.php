<?php

/**
 * This is the model class for table "et_drug_administration".
 *
 * The followings are the available columns in table 'et_drug_administration':
 * @property integer $id
 * @property int $element_id
 * @property int $assignment_id
 *
 * The followings are the available model relations:
 * @property Element_DrugAdministration $element_id
 * @property OphDrPGDPSD_PGDPSD $assignment_id
 */
// namespace OEModule\OphDrPGDPSD\models;
use OEModule\OphCiExamination\models\HistoryMedicationsStopReason;
class Element_DrugAdministration_record extends \EventMedicationUse
{
    public $taper_support = false;
    public function rules()
    {
        return array_merge(
            parent::rules(),
            array(
                array('dose, route_id', 'required'),
                array('laterality', 'safe'),
            )
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array_merge(
            parent::relations(),
            array(
                'event' => array(self::BELONGS_TO, 'Event', 'event_id', 'on' => 'event.deleted = 0'),
                'drug_admin' => array(self::BELONGS_TO, 'Element_DrugAdministration', array('event_id' => 'event_id')),
            )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_DrugAdministration_record the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getUsageType()
    {
        return "OphDrPGDPSD";
    }

    public static function getUsageSubtype()
    {
        return "DrugAdministration";
    }

    protected function afterSave()
    {
        return;
    }
}
