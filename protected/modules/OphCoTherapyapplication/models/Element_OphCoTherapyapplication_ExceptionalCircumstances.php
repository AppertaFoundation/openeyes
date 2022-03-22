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
 * This is the model class for table "et_ophcotherapya_exceptional".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property bool $left_standard_intervention_exists
 * @property int $left_standard_intervention_id
 * @property bool $left_standard_previous
 * @property bool $left_condition_rare
 * @property string $left_incidence
 * @property int $left_intervention_id
 * @property string $left_description
 * @property string $left_patient_different
 * @property string $left_patient_gain
 * @property int $left_patient_factors
 * @property string $left_patient_factor_details
 * @property string $left_patient_expectations
 * @property int $left_start_period_id
 * @property string $left_urgency_reason
 * @property bool $right_standard_intervention_exists
 * @property int $right_standard_intervention_id
 * @property bool $right_standard_previous
 * @property bool $right_condition_rare
 * @property string $right_incidence
 * @property int $right_intervention_id
 * @property string $right_description
 * @property string $right_patient_different
 * @property string $right_patient_gain
 * @property int $right_patient_factors
 * @property string $right_patient_factor_details
 * @property string $right_patient_expectations
 * @property int $right_start_period_id
 * @property string $right_urgency_reason
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property OphCoTherapyapplication_ExceptionalCircumstances_StandardIntervention $left_standard_intervention
 * @property OphCoTherapyapplication_ExceptionalCircumstances_StandardIntervention $right_standard_intervention
 * @property OphCoTherapyapplication_ExceptionalCircumstances_Intervention $left_intervention
 * @property OphCoTherapyapplication_ExceptionalCircumstances_Intervention $right_intervention
 * @property OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention[] $previnterventions
 * @property OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention[] $left_previnterventions
 * @property OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention[] $right_previnterventions
 * @property OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention[] $relevantinterventions
 * @property OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention[] $left_relevantinterventions
 * @property OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention[] $right_relevantinterventions
 * @property OphCoTherapyapplication_ExceptionalCircumstances_DeviationReasonAssignment[] $deviationreasons
 * @property OphCoTherapyapplication_ExceptionalCircumstances_DeviationReason[] $left_deviationreasons
 * @property OphCoTherapyapplication_ExceptionalCircumstances_DeviationReason[] $right_deviationreasons
 * @property OphCoTherapyapplication_ExceptionalCircumstances_StartPeriod $left_start_period
 * @property OphCoTherapyapplication_ExceptionalCircumstances_StartPeriod $right_start_period
 * @property OphCoTherapyapplication_ExceptionalCircumstances_FileCollectionAssignment[] $filecollection_assignments
 * @property OphCoTherapyapplication_FileCollection[] $left_filecollections
 * @property OphCoTherapyapplication_FileCollection[] $right_filecollections
 */
class Element_OphCoTherapyapplication_ExceptionalCircumstances extends SplitEventTypeElement
{
    public $service;

    protected $auto_update_relations = true;
    protected $relation_defaults = array(
        'left_previnterventions' => array(
                'is_relevant' => false,
                'exceptional_side_id' => Eye::LEFT,
        ),
        'right_previnterventions' => array(
                'is_relevant' => false,
                'exceptional_side_id' => Eye::RIGHT,
        ),
        'left_relevantinterventions' => array(
                'is_relevant' => true,
                'exceptional_side_id' => Eye::LEFT,
        ),
        'right_relevantinterventions' => array(
                'is_relevant' => true,
                'exceptional_side_id' => Eye::RIGHT,
        ),
        'left_deviationreasons' => array(
                'side_id' => Eye::LEFT,
        ),
        'right_deviationreasons' => array(
                'side_id' => Eye::RIGHT,
        ),
        'left_filecollections' => array(
                'exceptional_side_id' => Eye::LEFT,
        ),
        'right_filecollections' => array(
                'exceptional_side_id' => Eye::RIGHT,
        ),
    );

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'et_ophcotherapya_exceptional';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, left_standard_intervention_exists, left_standard_intervention_id, left_standard_previous,'.
                    'left_condition_rare, left_incidence, left_intervention_id, left_description, left_patient_different, '.
                    'left_patient_gain, left_patient_factors, left_patient_factor_details, left_start_period_id, left_urgency_reason,'.
                    'right_standard_intervention_exists, right_standard_intervention_id, right_standard_previous,'.
                    'right_condition_rare, right_incidence, right_intervention_id, right_description, '.
                    'right_patient_different, right_patient_gain, right_patient_factors, right_patient_factor_details, '.
                    'right_start_period_id, right_urgency_reason left_previnterventions, right_previnterventions,
					left_relevantinterventions, right_relevantinterventions, left_deviationreasons, right_deviationreasons,
					left_filecollections, right_filecollections', 'safe', ),
            array('left_standard_intervention_exists, left_patient_different, left_patient_gain, left_patient_factors,'.
                    'left_patient_expectations, left_start_period_id',    'requiredIfSide', 'side' => 'left', ),
            array('right_standard_intervention_exists, right_patient_different, right_patient_gain,  right_patient_factors,'.
                    'right_patient_expectations, right_start_period_id', 'requiredIfSide', 'side' => 'right', ),
            array('left_standard_intervention_id, left_standard_previous, left_intervention_id, left_description',
                    'requiredIfStandardInterventionExists', 'side' => 'left', ),
            array('right_standard_intervention_id, right_standard_previous, right_intervention_id, right_description',
                    'requiredIfStandardInterventionExists', 'side' => 'right', ),
            array('left_condition_rare, left_incidence',
                    'requiredIfStandardInterventionDoesNotExists', 'side' => 'left', ),
            array('right_condition_rare, right_incidence',
                    'requiredIfStandardInterventionDoesNotExists', 'side' => 'right', ),
            // TODO: validation of reasons for not using standard intervention
            array('left_deviationreasons', 'requiredIfStandardInterventionNotUsed', 'side' => 'left'),
            array('right_deviationreasons', 'requiredIfStandardInterventionNotUsed', 'side' => 'right'),
            array('left_patient_factor_details', 'requiredIfPatientFactors', 'side' => 'left'),
            array('right_patient_factor_details', 'requiredIfPatientFactors', 'side' => 'right'),
            array('left_urgency_reason', 'requiredIfUrgent', 'side' => 'left'),
            array('right_urgency_reason', 'requiredIfUrgent', 'side' => 'right'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id, left_standard_intervention_exists, left_standard_intervention_id, left_standard_previous,'.
                    'left_condition_rare, left_incidence, left_details, left_intervention_id, left_description, left_patient_different, '.
                    'left_patient_gain, left_patient_factors, left_patient_factor_details, left_patient_expectations, '.
                    'left_start_period_id, left_urgency_reason, right_standard_intervention_exists, right_standard_intervention_id,'.
                    'right_standard_previous, right_condition_rare, right_incidence, right_details, right_intervention_id,'.
                    'right_description, right_patient_different, right_patient_gain, right_patient_factors, right_patient_factor_details,'.
                    'right_patient_expectations, right_start_period_id, right_urgency_reason', 'safe', 'on' => 'search', ),
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'left_standard_intervention' => array(self::BELONGS_TO,
                'OphCoTherapyapplication_ExceptionalCircumstances_StandardIntervention',
                'left_standard_intervention_id', ),
            'right_standard_intervention' => array(self::BELONGS_TO,
                'OphCoTherapyapplication_ExceptionalCircumstances_StandardIntervention',
                'right_standard_intervention_id', ),
            'left_intervention' => array(self::BELONGS_TO,
                'OphCoTherapyapplication_ExceptionalCircumstances_Intervention',
                'left_intervention_id', ),
            'right_intervention' => array(self::BELONGS_TO,
                'OphCoTherapyapplication_ExceptionalCircumstances_Intervention',
                'right_intervention_id', ),
            'previnterventions' => array(self::HAS_MANY, 'OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', 'exceptional_id',
                'condition' => 'is_relevant = :relevant',
                'params' => array(':relevant' => false), ),
            'left_previnterventions' => array(self::HAS_MANY, 'OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', 'exceptional_id',
                'condition' => 'is_relevant = :relevant AND exceptional_side_id = :side',
                'params' => array(':relevant' => false, ':side' => Eye::LEFT), ),
            'right_previnterventions' => array(self::HAS_MANY, 'OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', 'exceptional_id',
                'condition' => 'is_relevant = :relevant AND exceptional_side_id = :side',
                'params' => array(':relevant' => false, ':side' => Eye::RIGHT), ),
            'relevantinterventions' => array(self::HAS_MANY, 'OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', 'exceptional_id',
                'condition' => 'is_relevant = :relevant',
                'params' => array(':relevant' => true), ),
            'left_relevantinterventions' => array(self::HAS_MANY, 'OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', 'exceptional_id',
                'condition' => 'is_relevant = :relevant AND exceptional_side_id = :side',
                'params' => array(':relevant' => true, ':side' => Eye::LEFT), ),
            'right_relevantinterventions' => array(self::HAS_MANY, 'OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', 'exceptional_id',
                'condition' => 'is_relevant = :relevant AND exceptional_side_id = :side',
                'params' => array(':relevant' => true, ':side' => Eye::RIGHT), ),
            'deviationreasons' => array(self::HAS_MANY, 'OphCoTherapyapplication_ExceptionalCircumstances_DeviationReasonAssignment', 'element_id'),
            'left_deviationreasons' => array(self::HAS_MANY, 'OphCoTherapyapplication_ExceptionalCircumstances_DeviationReason', 'deviationreason_id',
                'through' => 'deviationreasons', 'on' => 'deviationreasons.side_id = '.Eye::LEFT, ),
            'right_deviationreasons' => array(self::HAS_MANY, 'OphCoTherapyapplication_ExceptionalCircumstances_DeviationReason', 'deviationreason_id',
                'through' => 'deviationreasons', 'on' => 'deviationreasons.side_id = '.Eye::RIGHT, ),
            'left_start_period' => array(self::BELONGS_TO, 'OphCoTherapyapplication_ExceptionalCircumstances_StartPeriod', 'left_start_period_id'),
            'right_start_period' => array(self::BELONGS_TO, 'OphCoTherapyapplication_ExceptionalCircumstances_StartPeriod', 'right_start_period_id'),
            'filecollection_assignments' => array(self::HAS_MANY, 'OphCoTherapyapplication_ExceptionalCircumstances_FileCollectionAssignment', 'exceptional_id'),
            'left_filecollections' => array(self::HAS_MANY, 'OphCoTherapyapplication_FileCollection', 'collection_id',
                'through' => 'filecollection_assignments', 'on' => 'filecollection_assignments.exceptional_side_id = '.Eye::LEFT, ),
            'right_filecollections' => array(self::HAS_MANY, 'OphCoTherapyapplication_FileCollection', 'collection_id',
                'through' => 'filecollection_assignments', 'on' => 'filecollection_assignments.exceptional_side_id = '.Eye::RIGHT, ),
        );
    }

    public function sidedFields()
    {
        return array('standard_intervention_exists', 'standard_intervention_id', 'standard_previous', 'condition_rare',
                    'incidence', 'intervention_id', 'description', 'patient_different', 'patient_gain', 'patient_factors',
                    'patient_factor_details', 'patient_expectations', 'start_period_id', 'urgency_reason', );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'id' => 'ID',
            'event_id' => 'Event',
            'left_standard_intervention_exists' => 'Is there a standard intervention at this stage',
            'left_standard_intervention_id' => 'Standard intervention',
            'left_standard_previous' => 'The standard intervention has been previously used',
            'left_condition_rare' => 'Is this ocular condition rare?',
            'left_incidence' => 'What is the incidence?',
            'left_intervention_id' => 'The (non-standard) intervention applying for funding to be used is',
            'left_description' => 'Description',
            'left_deviationreasons' => 'The reason for not using standard intervention',
            'left_patient_different' => 'How is the patient significantly different to others with the same condition?',
            'left_patient_gain' => 'How is the patient likely to gain more benefit than otherwise?',
            'left_patient_factors' => 'Patient Factors',
            'left_patient_factor_details' => 'Patient Factor Details',
            'left_previnterventions' => 'Previous Interventions',
            'left_relevantinterventions' => 'Relevant Interventions',
            'left_patient_expectations' => 'Patient Expectations',
            'left_start_period_id' => 'Anticipated Start Date',
            'left_urgency_reason' => 'State clinical reason for urgency',
            'left_filecollections' => 'File Sets',
            'right_standard_intervention_exists' => 'Standard Intervention Exists',
            'right_standard_intervention_id' => 'Standard intervention',
            'right_standard_previous' => 'The standard intervention has been previously used',
            'right_condition_rare' => 'Is this ocular condition rare?',
            'right_incidence' => 'What is the incidence?',
            'right_intervention_id' => 'The (non-standard) intervention applying for funding to be used is',
            'right_description' => 'Description',
            'right_deviationreasons' => 'The reason for not using standard intervention',
            'right_patient_different' => 'How is the patient significantly different to others with the same condition?',
            'right_patient_gain' => 'How is the patient likely to gain more benefit than otherwise?',
            'right_patient_factors' => 'Patient Factors',
            'right_patient_factor_details' => 'Patient Factor Details',
            'right_previnterventions' => 'Previous Interventions',
            'right_relevantinterventions' => 'Relevant Interventions',
            'right_patient_expectations' => 'Patient Expectations',
            'right_start_period_id' => 'Anticipated Start Date',
            'right_urgency_reason' => 'State clinical reason for urgency',
            'right_filecollections' => 'File Sets',
        );
        foreach (array('left', 'right') as $side) {
            if ($this->{$side.'_intervention'}) {
                $labels[$side.'_description'] = $this->{$side.'_intervention'}->description_label;
            }
        }

        return $labels;
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();
        //TODO: update search criteria
        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('standard_intervention_exists', $this->standard_intervention_exists);
        $criteria->compare('details', $this->details);
        $criteria->compare('interventions_id', $this->interventions_id);
        $criteria->compare('description', $this->description);
        $criteria->compare('patient_factors', $this->patient_factors);
        $criteria->compare('patient_factor_details', $this->patient_factor_details);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    protected function beforeSave()
    {
        return parent::beforeSave();
    }

    protected function afterSave()
    {
        return parent::afterSave();
    }

    protected function beforeValidate()
    {
        return parent::beforeValidate();
    }

    protected function afterValidate()
    {
        foreach (array('left' => 'hasLeft', 'right' => 'hasRight') as $side => $checkFunc) {
            if ($this->$checkFunc()) {
                foreach ($this->{$side.'_previnterventions'} as $i => $prev) {
                    if (!$prev->validate()) {
                        foreach ($prev->getErrors() as $fld => $err) {
                            $this->addError($side.'_previntervention', ucfirst($side).' previous intervention ('.($i + 1).'): '.implode(', ', $err));
                        }
                    }
                }
                foreach ($this->{$side.'_relevantinterventions'} as $i => $relevant) {
                    if (!$relevant->validate()) {
                        foreach ($relevant->getErrors() as $fld => $err) {
                            $this->addError($side.'_relevantintervention', ucfirst($side).' relevant intervention ('.($i + 1).'): '.implode(', ', $err));
                        }
                    }
                }
            }
        }
    }

    /**
     * get list of valid standard interventions for this element on the given side.
     *
     * @param string $side
     *
     * @return OphCoTherapyapplication_ExceptionalCircumstances_StandardIntervention[]
     */
    public function getStandardInterventionsForSide($side)
    {
        return OphCoTherapyapplication_ExceptionalCircumstances_StandardIntervention::model()->activeOrPk($this->{$side.'_standard_intervention_id'})->findAll();
    }

    /**
     * Returns whether we need to the deviation reason to be populated for the given side.
     *
     * @param string $side
     *
     * @return bool
     */
    public function needDeviationReasonForSide($side)
    {
        if ($this->{$side.'_standard_previous'} == false
            && ($this->{$side.'_intervention'} && $this->{$side.'_intervention'}->is_deviation)) {
            return true;
        }

        return false;
    }

    /**
     * get the possible deviation reasons for the side (encapsulated to ensure we get any reasons that may
     * no longer be enabled).
     *
     * @param stringh $side
     *
     * @return OphCoTherapyapplication_ExceptionalCircumstances_DeviationReason[]
     */
    public function getDeviationReasonsForSide($side)
    {
        $in_use_reason_ids = array();
        if ( is_array($this->{$side.'_deviationreasons'}) ) {
            foreach ($this->{$side.'_deviationreasons'} as $curr) {
                $in_use_reason_ids[] = $curr->id;
            }
        }

        return OphCoTherapyapplication_ExceptionalCircumstances_DeviationReason::model()->activeOrPk($in_use_reason_ids)->findAll();
    }

    /**
     * get the valid stat periods for the given side.
     *
     * @param type $side
     *
     * @return type
     */
    public function getStartPeriodsForSide($side)
    {
        return OphCoTherapyapplication_ExceptionalCircumstances_StartPeriod::model()->activeOrPk($this->{$side.'_start_period_id'})->findAll();
    }

    /**
     * check that the standard intervention description is given if the element is flagged appropriately.
     *
     * @param string $attribute
     * @param array  $params
     */
    public function requiredIfStandardInterventionExists($attribute, $params)
    {
        if (($params['side'] == 'left' && $this->eye_id != Eye::RIGHT) || ($params['side'] == 'right' && $this->eye_id != Eye::LEFT)) {
            if ($this->{$params['side'].'_standard_intervention_exists'} && $this->$attribute == null) {
                $this->addError($attribute, ucfirst($params['side']).' '.$this->getAttributeLabel($attribute).' cannot be blank.');
            }
        }
    }

    /*
     * requirement validation if a standard intervention does not exist
    */
    public function requiredIfStandardInterventionDoesNotExists($attribute, $params)
    {
        if (($params['side'] == 'left' && $this->eye_id != Eye::RIGHT) || ($params['side'] == 'right' && $this->eye_id != Eye::LEFT)) {
            if ($this->{$params['side'].'_standard_intervention_exists'} != null
                && $this->{$params['side'].'_standard_intervention_exists'} == false
                && $this->$attribute == null) {
                $this->addError($attribute, ucfirst($params['side']).' '.$this->getAttributeLabel($attribute).' cannot be blank.');
            }
        }
    }

    /**
     * validates attribute if there is standard intervention and it is not being used.
     *
     * @param string $attribute
     * @param array  $params
     */
    public function requiredIfStandardInterventionNotUsed($attribute, $params)
    {
        if (($params['side'] == 'left' && $this->eye_id != Eye::RIGHT) || ($params['side'] == 'right' && $this->eye_id != Eye::LEFT)) {
            if ($this->{$params['side'].'_standard_intervention_exists'} && !$this->{$params['side'].'_standard_previous'}
                && $this->{$params['side'].'_intervention'} && $this->{$params['side'].'_intervention'}->is_deviation
                && !$this->$attribute) {
                $this->addError($attribute, ucfirst($params['side']).' '.$this->getAttributeLabel($attribute).' cannot be blank.');
            }
        }
    }

    /**
     * @param string $attribute
     * @param array  $params
     */
    public function requiredIfPatientFactors($attribute, $params)
    {
        if (($params['side'] == 'left' && $this->eye_id != Eye::RIGHT) || ($params['side'] == 'right' && $this->eye_id != Eye::LEFT)) {
            if ($this->{$params['side'].'_patient_factors'} && $this->$attribute == null) {
                $this->addError($attribute, ucfirst($params['side']).' '.$this->getAttributeLabel($attribute).' cannot be blank.');
            }
        }
    }

    /**
     * validation of an attribute if the $side start_period is flagged as urgent.
     *
     * @param string $attribute
     * @param array  $params
     */
    public function requiredIfUrgent($attribute, $params)
    {
        $side = $params['side'];
        if (($side == 'left' && $this->eye_id != Eye::RIGHT) || ($side == 'right' && $this->eye_id != Eye::LEFT)) {
            if ($this->$attribute == null && ($this->{$side.'_start_period'} && $this->{$side.'_start_period'}->urgent)) {
                $this->addError($attribute, ucfirst($params['side']).' '.$this->getAttributeLabel($attribute).' cannot be blank.');
            }
        }
    }

    /**
     * Get ids of the file collections in use by the element for a given side.
     */
    public function getFileCollectionValuesForSide($side)
    {
        $file_collection_values = array();

        if ( is_array($this->{$side.'_filecollections'}) ) {
            foreach ($this->{$side.'_filecollections'} as $file_collection) {
                $file_collection_values[] = $file_collection->id;
            }
        }

        return $file_collection_values;
    }

    /**
     * Is this a required element to be displayed in the UI?
     * This element is not required by default, but we still want to prevent
     * users from removing it in the UI.
     *
     * @return bool
     */
    public function isRequiredInUI()
    {
        return true;
    }

    public function getContainer_form_view()
    {
        return '//patient/element_container_form_no_bin';
    }
}
