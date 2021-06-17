<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "et_ophtroperationnote_cataract".
 *
 * The followings are the available columns in table 'et_ophtroperationnote_cataract':
 *
 * @property int $id
 * @property int $event_id
 * @property int $incision_site_id
 * @property string $length
 * @property string $meridian
 * @property int $incision_type_id
 * @property string $eyedraw
 * @property string $report
 * @property int $iol_position_id
 * @property string $complication_notes
 * @property string $eyedraw2
 * @property string $iol_power
 * @property int $iol_type_id
 * @property string $report2
 * @property string $comments
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property OphTrOperationnote_IncisionType $incision_type
 * @property OphTrOperationnote_IncisionSite $incision_site
 * @property OphTrOperationnote_IOLPosition $iol_position
 * @property OphTrOperationnote_CataractComplication[] $complication_assignments
 * @property OphTrOperationnote_CataractComplications[] $complicationItems
 * @property OphTrOperationnote_CataractOperativeDevice[] $operative_device_assigments
 * @property OperativeDevice[] $operative_devices
 * @property OphTrOperationnote_IOLType $iol_type
 */
class Element_OphTrOperationnote_Cataract extends Element_OnDemandEye
{
    public $predicted_refraction = null;
    public $requires_eye = true;

    protected static $procedure_doodles = array(
        array('doodle_class' => 'PhakoIncision',
            'unless' => array('PhakoIncision')
        ),
        array('doodle_class' => 'PCIOL',
            'unless' => array('PCIOL', 'ACIOL', 'ToricPCIOL')
        )
    );
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphTrOperationnote_Cataract the static model class
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
        return 'et_ophtroperationnote_cataract';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, incision_site_id, length, meridian, incision_type_id, iol_position_id, iol_type_id, iol_power, eyedraw, report, complication_notes, eyedraw2, report2, predicted_refraction, pcr_risk, phaco_cde , comments', 'safe'),
            array('incision_site_id, length, meridian, incision_type_id, iol_position_id, eyedraw, report, eyedraw2', 'required'),
            array('length', 'numerical', 'integerOnly' => false, 'numberPattern' => '/^[0-9](\.[0-9])?$/', 'message' => 'Length must be 0 - 9.9 in increments of 0.1'),
            array('meridian', 'numerical', 'integerOnly' => false, 'numberPattern' => '/^[0-9]{1,3}(\.[0-9])?$/', 'min' => 000, 'max' => 360, 'message' => 'Meridian must be 000.5 - 360.0 degrees'),
            array('phaco_cde', 'numerical' , 'integerOnly' => false, 'message'=>'Phaco CDE need to be a numeric value'),
            array('phaco_cde', 'default', 'setOnEmpty' => true, 'value' => null),
            array('pcr_risk', 'default', 'setOnEmpty' => true, 'value' => null),

            array('iol_type_id', 'validateIolType'),
            array('predicted_refraction', 'validatePredictedRefraction'),
            array('iol_power', 'validateIolpower'),

            array('complications', 'validateComplications'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            //array('id, event_id, incision_site_id, length, meridian, incision_type_id, eyedraw, report, wound_burn, iris_trauma, zonular_dialysis, pc_rupture, decentered_iol, iol_exchange, dropped_nucleus, op_cancelled, corneal_odema, iris_prolapse, zonular_rupture, vitreous_loss, iol_into_vitreous, other_iol_problem, choroidal_haem', 'on' => 'search'),
        );
    }

    /**
     * Check if iol position is not 'None'
     */
    public function validateIolType()
    {
        $none_position = $this->getNoneIolPosition();

        if (isset(Yii::app()->request->getPost('Element_OphTrOperationnote_Cataract')['iol_position_id'])) {
            if (Yii::app()->request->getPost('Element_OphTrOperationnote_Cataract')['iol_position_id'] != $none_position->id) {
                if (!$this->iol_type_id) {
                    $this->addError('iol_type_id', 'IOL type cannot be blank');
                }

                if (!isset($this->iol_power)) {
                    $this->addError('iol_power', 'IOL power cannot be blank');
                }
            }
        }
    }

    /**
     * Validate Predicted Refraction if IOL is part of the element.
     *
     * @return bool
     */
    public function validatePredictedRefraction()
    {
        $none_position = $this->getNoneIolPosition();

        if (isset(Yii::app()->request->getPost('Element_OphTrOperationnote_Cataract')['iol_position_id'])) {
            if (Yii::app()->request->getPost('Element_OphTrOperationnote_Cataract')['iol_position_id'] != $none_position->id) {
                $value = $this->predicted_refraction;
                if (!preg_match('/^\-?[0-9]{1,2}(\.[0-9]{1,2})?$/', $value)) {
                    $message = $this->addError('predicted_refraction', 'Predicted refraction must be between -30.00 and 30.00');
                } elseif ($value < -30 || $value > 30) {
                    $message = $this->addError('predicted_refraction', 'Predicted refraction must be between -30.00 and 30.00');
                }
            }
        }
    }
    /**
     * Validate Iol Power if IOL is part of the element.
     *
     * @return bool
     */
    public function validateIolPower()
    {
        $none_position = $this->getNoneIolPosition();

        if (isset(Yii::app()->request->getPost('Element_OphTrOperationnote_Cataract')['iol_position_id'])) {
            if (Yii::app()->request->getPost('Element_OphTrOperationnote_Cataract')['iol_position_id'] != $none_position->id) {
                $value = $this->iol_power;
                if (!preg_match('/^\-?[0-9]{1,2}(\.[0-9]{1,2})?$/', $value)) {
                    $message = $this->addError('iol_power', 'IOL power must be a number with an optional two decimal places between -10.00 and 40.00');
                } elseif ($value < -10 || $value > 40) {
                    $message = $this->addError('iol_power', 'IOL Power must be between -10 to 40');
                }
            }
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'incision_type' => array(self::BELONGS_TO, 'OphTrOperationnote_IncisionType', 'incision_type_id'),
            'incision_site' => array(self::BELONGS_TO, 'OphTrOperationnote_IncisionSite', 'incision_site_id'),
            'iol_position' => array(self::BELONGS_TO, 'OphTrOperationnote_IOLPosition', 'iol_position_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'complication_assignments' => array(self::HAS_MANY, 'OphTrOperationnote_CataractComplication', 'cataract_id'),
            'complications' => array(self::HAS_MANY, 'OphTrOperationnote_CataractComplications', 'complication_id',
                'through' => 'complication_assignments', ),
            'operative_device_assignments' => array(self::HAS_MANY, 'OphTrOperationnote_CataractOperativeDevice', 'cataract_id'),
            'operative_devices' => array(self::HAS_MANY, 'OperativeDevice', 'operative_device_id',
                'through' => 'operative_device_assignments', ),
        );
    }

    public function getIol_type()
    {
        $model_name = "OphTrOperationnote_IOLType";

        if (\Yii::app()->hasModule("OphInBiometry")) {
            $model_name = "OphInBiometry_LensType_Lens";
        }

        return $model_name::model()->findByPk($this->iol_type_id);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'incision_site_id' => 'Incision site',
            'incision_type_id' => 'Incision type',
            'iol_position_id' => 'IOL position',
            'iol_power' => 'IOL power',
            'iol_type_id' => 'IOL type',
            'length' => 'Length',
            'meridian' => 'Meridian',
            'report' => 'Details',
            'complication_notes' => 'Complication notes',
            'report2' => 'Details',
            'predicted_refraction' => 'Predicted refraction',
            'pcr_risk' => 'PCR Risk',
            'phaco_cde' => 'Phaco CDE',
            'comments' => 'Comments'
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
        $criteria->compare('event_id', $this->event_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    protected function beforeSave()
    {
        $position_none = $this->getNoneIolPosition();

        //if (!isset(Yii::app()->request->getPost('Element_OphTrOperationnote_Cataract')['iol_position_id'])){
        if (! $this->iol_position_id || $this->iol_position_id == $position_none->id) {
            $this->iol_power = null;
            $this->iol_type_id = null;
            $this->predicted_refraction = null;
            $this->iol_position_id = $position_none->id;
        }
        return parent::beforeSave();
    }

    /**
     * Need to delete associated records, and any doodles shredded out of this element for object persistence
     *
     * @see CActiveRecord::beforeDelete()
     */
    protected function beforeDelete()
    {
        OphTrOperationnote_CataractComplication::model()->deleteAllByAttributes(array('cataract_id' => $this->id));
        OphTrOperationnote_CataractOperativeDevice::model()->deleteAllByAttributes(array('cataract_id' => $this->id));

        $processor = new \EDProcessor();
        $processor->removeElementEyedraws($this);

        return parent::beforeDelete();
    }

    /**
     * Update the complications on the element.
     *
     * @param $complication_ids
     *
     * @throws Exception
     */
    public function updateComplications($complication_ids)
    {
        $curr_by_id = array();

        foreach ($this->complication_assignments as $ca) {
            $curr_by_id[$ca->complication_id] = $ca;
        }

        foreach ($complication_ids as $c_id) {
            if (!isset($curr_by_id[$c_id])) {
                $ca = new OphTrOperationnote_CataractComplication();
                $ca->cataract_id = $this->id;
                $ca->complication_id = $c_id;
                if (!$ca->save()) {
                    throw new Exception('Unable to save complication assignment: '.print_r($ca->getErrors(), true));
                }
            } else {
                unset($curr_by_id[$c_id]);
            }
        }

        foreach ($curr_by_id as $ca) {
            if (!$ca->delete()) {
                throw new Exception('Unable to delete complication assignment: '.print_r($ca->getErrors(), true));
            }
        }
    }

    /**
     * Update the operative devices on the element.
     *
     * @param $operative_device_ids
     *
     * @throws Exception
     */
    public function updateOperativeDevices($operative_device_ids)
    {
        $curr_by_id = array();

        if (is_array($this->operative_device_assignments)) {
            foreach ($this->operative_device_assignments as $oda) {
                $curr_by_id[$oda->operative_device_id] = $oda;
            }
        }

        if (is_array($operative_device_ids)) {
            foreach ($operative_device_ids as $od_id) {
                if (!isset($curr_by_id[$od_id])) {
                    $oda = new OphTrOperationnote_CataractOperativeDevice();
                    $oda->cataract_id = $this->id;
                    $oda->operative_device_id = $od_id;

                    if (!$oda->save()) {
                        throw new Exception('Unable to save operative device assignment: '.print_r($oda->getErrors(), true));
                    }
                } else {
                    unset($curr_by_id[$od_id]);
                }
            }
        }

        if (is_array($curr_by_id)) {
            foreach ($curr_by_id as $oda) {
                if (!$oda->delete()) {
                    throw new Exception('Unable to delete operative device assignment: '.print_r($oda->getErrors(), true));
                }
            }
        }
    }

    /**
     * Validate IOL data if IOL is part of the element.
     *
     * @return bool
     */
    public function beforeValidate()
    {
        /*
        $iol_position = OphTrOperationnote_IOLPosition::model()->findByPk($this->iol_position_id);

        if (!$iol_position || $iol_position->name != 'None') {
            if (!$this->iol_type_id) {
                $this->addError('iol_type_id', 'IOL type cannot be blank');
            }

            if (!isset($this->iol_power)) {
                $this->addError('iol_power', 'IOL power cannot be blank');
            }
            /* elseif (!is_numeric($this->iol_power) || strlen(substr(strrchr($this->iol_power, "."), 1)) > 2 || ((-999.99 > $this->iol_power) || ($this->iol_power > 999.99))) {
                $this->addError('iol_power', 'IOL power must be a number with an optional two decimal places between -999.99 and 999.99');
              }
              elseif (strlen(substr(strrchr($this->iol_power, "."), 1)) > 2) {
                $this->addError('iol_power', 'IOL power must be a number with an optional two decimal places between -999.99 and 999.99');
            } elseif ((-999.99 > $this->iol_power) || ($this->iol_power > 999.99)) {
                $this->addError('iol_power', 'IOL power must be a number with an optional two decimal places between -999.99 and 999.99');
              } */
        /*}
        */
        return parent::beforeValidate();
    }

    /**
     * Check the eye draw for any IOL elements. If there is one, IOL fields should not be hidden.
     *
     * @return bool
     */
    public function getIol_hidden()
    {
        OELog::log($this->eyedraw);
        if ($eyedraw = @json_decode($this->eyedraw)) {
            if (is_array($eyedraw)) {
                foreach ($eyedraw as $object) {
                    if (in_array($object->subclass, Yii::app()->params['eyedraw_iol_classes'])) {
                        return false;
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Get ids of cataract complications associated with the element.
     */
    public function getCataractComplicationValues()
    {
        $complication_values = array();

        foreach ($this->complication_assignments as $complication_assignment) {
            $complication_values[] = $complication_assignment->complication_id;
        }

        return $complication_values;
    }

    protected function afterConstruct()
    {
        if ($this->isNewRecord) {
            $element_type = ElementType::model()->find('class_name = :class_name', array(':class_name' => get_class($this)));
            $incision_length = SettingUser::model()->find('user_id = :user_id AND element_type_id = :element_type_id AND `key` = :key', array(':user_id' => Yii::app()->user->id, ':element_type_id' => $element_type->id, ':key' => 'incision_length'));

            if ($incision_length) {
                $this->length = $incision_length->value;
            } elseif (isset(Yii::app()->session['selected_firm_id'])) {
                $defaultLengthRecord = OphTrOperationnote_CataractIncisionLengthDefault::model()->findByAttributes(
                    array('firm_id' => (int) Yii::app()->session['selected_firm_id'])
                );

                if ($defaultLengthRecord) {
                    $this->length = $defaultLengthRecord->value;
                } elseif (isset(Yii::app()->params['default_incision_length']) && Yii::app()->params['default_incision_length'] !== '') {
                    $this->length = Yii::app()->params['default_incision_length'];
                }
            }
        }

        parent::afterConstruct();
    }

    /**
     * Validate complications.
     */
    public function validateComplications()
    {
        $complications_none = OphTrOperationnote_CataractComplications::model()->findByAttributes(array('name'=>'None'));
        $noneId = $complications_none->id;

        $complications = Yii::app()->request->getPost('OphTrOperationnote_CataractComplications');
        if (!$complications || !count($complications)) {
            if (!$this->complications || !count($this->complications)) {
                $this->addError('Complications', 'Cataract Complications cannot be blank.');
            }
        } else {
            foreach ($complications as $complication) {
                if ($complication == $noneId && count($complications) > 1) {
                    $this->addError('Complications', 'Cataract Complications cannot be none and any other complication.');
                }
            }
        }
    }


    /**
     * Returns comma separated list of complications on this procedure note.
     *
     * @param $default
     *
     * @return string
     */
    public function getComplicationsString($default = 'None')
    {
        $res = array();
        foreach ($this->complications as $comp) {
            $res[] = $comp->name;
        }
        if ($this->complication_notes) {
            $res[] = $this->complication_notes;
        }
        if ($res) {
            return implode(', ', $res);
        } else {
            return $default;
        }
    }

    private function getNoneIolPosition()
    {
            $position_none = OphTrOperationnote_IOLPosition::model()->findByAttributes(array('name'=>'None'));
        if ($position_none) {
            return $position_none;
        } else {
            return false;
        }
    }

     /**
     * Load in the correction values for the eyedraw fields
     *
     * @param Patient|null $patient
     * @throws \CException
     */
    public function setDefaultOptions(Patient $patient = null)
    {
        if ($patient === null) {
            throw new \CException('patient object required for setting ' . get_class($this) . ' default options');
        }
        if ((int)$this->getEye()->id === 1) {
            $this->meridian = 0;
        }
        parent::setDefaultOptions($patient);

        $processor = new \EDProcessor();
        $processor->loadElementEyedrawDoodles($patient, $this, $this->getEye()->id, 'eyedraw');
        // current way of handling the default doodles to add to the eyedraw for the procedure
        // this will hopefully be replaced when we have the ability to store preferences for users
        // as to their default doodle set for the cataract procedure.
        $processor->addElementEyedrawDoodles($this, 'eyedraw', static::$procedure_doodles);
    }

    /**
     * Performs the shredding of Eyedraw data for the patient record
     *
     * @inheritdoc
     */
    public function afterSave()
    {
        $processor = new \EDProcessor();
        $processor->shredElementEyedraws($this, array('eyedraw' => (int)$this->getEye()->id));
        parent::afterSave();
    }

    public function getContainer_form_view()
    {
        return false;
    }
}
