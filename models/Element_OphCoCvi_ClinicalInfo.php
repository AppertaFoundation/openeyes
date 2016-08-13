<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCoCvi\models;

/**
 * This is the model class for table "et_ophcocvi_clinicinfo".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $event_id
 * @property string $examination_date
 * @property integer $is_considered_blind
 * @property integer $sight_varies_by_light_levels
 * @property string $unaided_right_va
 * @property string $unaided_left_va
 * @property string $best_corrected_right_va
 * @property string $best_corrected_left_va
 * @property string $best_corrected_binocular_va
 * @property integer $low_vision_status_id
 * @property integer $field_of_vision_id
 * @property string $diagnoses_not_covered
 * @property integer $consultant_id
 *
 * The followings are the available model relations:
 *
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property OphCoCvi_ClinicalInfo_LowVisionStatus $low_vision_status
 * @property OphCoCvi_ClinicalInfo_FieldOfVision $field_of_vision
 * @property Element_OphCoCvi_ClinicalInfo_Disorders_Assignment $disorderss
 * @property User $consultant
 */

class Element_OphCoCvi_ClinicalInfo extends \BaseEventTypeElement
{
    public static $BLIND_STATUS = "Severely Sight Impaired";
    public static $NOT_BLIND_STATUS = "Sight Impaired";

    /**
     * Returns the static model of the specified AR class.
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
        return 'et_ophcocvi_clinicinfo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array(
                'event_id, examination_date, is_considered_blind, sight_varies_by_light_levels, unaided_right_va, unaided_left_va, best_corrected_right_va, best_corrected_left_va, best_corrected_binocular_va, low_vision_status_id, field_of_vision_id, diagnoses_not_covered, consultant_id, ',
                'safe'
            ),
            array(
                'id, event_id, examination_date, is_considered_blind, sight_varies_by_light_levels, unaided_right_va, unaided_left_va, best_corrected_right_va, best_corrected_left_va, best_corrected_binocular_va, low_vision_status_id, field_of_vision_id, diagnoses_not_covered, consultant_id, ',
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
        return array(
            'element_type' => array(
                self::HAS_ONE,
                'ElementType',
                'id',
                'on' => "element_type.class_name='" . get_class($this) . "'"
            ),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'low_vision_status' => array(
                self::BELONGS_TO,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_LowVisionStatus',
                'low_vision_status_id'
            ),
            'field_of_vision' => array(
                self::BELONGS_TO,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_FieldOfVision',
                'field_of_vision_id'
            ),
            // probably more interested in relationship direct to disorders through this relation
            'disorders' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment',
                'element_id'
            ),
            'consultant' => array(self::BELONGS_TO, 'User', 'consultant_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'examination_date' => 'Examination date',
            'is_considered_blind' => 'Is considered blind',
            'sight_varies_by_light_levels' => 'Sight varies by light levels',
            'unaided_right_va' => 'Unaided right VA',
            'unaided_left_va' => 'Unaided left VA',
            'best_corrected_right_va' => 'Best corrected right VA',
            'best_corrected_left_va' => 'Best corrected left VA',
            'best_corrected_binocular_va' => 'Best corrected binocular VA',
            'low_vision_status_id' => 'Low vision status',
            'field_of_vision_id' => 'Field of vision',
            'disorders' => 'Disorders',
            'diagnoses_not_covered' => 'Diagnoses not covered',
            'consultant_id' => 'Consultant',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('examination_date', $this->examination_date);
        $criteria->compare('is_considered_blind', $this->is_considered_blind);
        $criteria->compare('sight_varies_by_light_levels', $this->sight_varies_by_light_levels);
        $criteria->compare('unaided_right_va', $this->unaided_right_va);
        $criteria->compare('unaided_left_va', $this->unaided_left_va);
        $criteria->compare('best_corrected_right_va', $this->best_corrected_right_va);
        $criteria->compare('best_corrected_left_va', $this->best_corrected_left_va);
        $criteria->compare('best_corrected_binocular_va', $this->best_corrected_binocular_va);
        $criteria->compare('low_vision_status_id', $this->low_vision_status_id);
        $criteria->compare('field_of_vision_id', $this->field_of_vision_id);
        $criteria->compare('disorders', $this->disorders);
        $criteria->compare('diagnoses_not_covered', $this->diagnoses_not_covered);
        $criteria->compare('consultant_id', $this->consultant_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    protected function afterSave()
    {
        $sides = array('2'=>'right','1'=>'left');
        foreach($sides as $side_value=>$side) {
            if (!empty($_POST['ophcocvi_clinicinfo_disorder_id_'.$side])) {
                $existing_comment_ids = array();
                foreach (Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments::model()
                             ->findAll('element_id = :elementId',
                                 array(':elementId' => $this->id)) as $item) {
                    $existing_comment_ids[] = $item->ophcocvi_clinicinfo_disorder_section_id;
                }
                $existing_assignment_ids = array();
                foreach (Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()
                             ->findAll('element_id = :elementId and eye_id = :eye_id',
                                 array(':elementId' => $this->id,'eye_id'=>$side_value)) as $item) {
                    $existing_assignment_ids[] = $item->ophcocvi_clinicinfo_disorder_id;
                }
                foreach ($_POST['ophcocvi_clinicinfo_disorder_section_id'] as $sectionId) {
                    foreach ($_POST['ophcocvi_clinicinfo_disorder_id_'.$side] as $id) {
                        if(isset($_POST['affected_'.$side][$sectionId][$id]) && $_POST['affected_'.$side][$sectionId][$id] == 1 && !in_array($id,$existing_assignment_ids)) {
                            $disorders = new Element_OphCoCvi_ClinicalInfo_Disorder_Assignment;
                            $disorders->element_id = $this->id;
                            $disorders->eye_id = $side_value;
                            $disorders->ophcocvi_clinicinfo_disorder_id = $id;
                            $disorders->affected = $_POST['affected_'.$side][$sectionId][$id];
                            $disorders->main_cause = isset($_POST['main_cause_'.$side][$sectionId][$id]) ? $_POST['main_cause_'.$side][$sectionId][$id] : 0;
                            if (!$disorders->save()) {
                                throw new Exception('Unable to save MultiSelect item: '.print_r($disorders->getErrors(),true));
                            }
                        }
                        else if(isset($_POST['affected_'.$side][$sectionId][$id])) {
                            $criteria = new \CDbCriteria;
                            $criteria->compare('element_id', $this->id);
                            $criteria->compare('eye_id', $side_value);
                            $criteria->compare('ophcocvi_clinicinfo_disorder_id', $id);
                            $disorders = Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()->find($criteria);
                            if(isset($disorders)) {
                                $disorders->affected = $_POST['affected_'.$side][$sectionId][$id];
                                $disorders->main_cause = (isset($_POST['main_cause_'.$side][$sectionId][$id]) &&
                                    isset($_POST['affected_'.$side][$sectionId][$id]) &&
                                    ($_POST['affected_'.$side][$sectionId][$id] == 1)) ?
                                        $_POST['main_cause_'.$side][$sectionId][$id] : 0;
                                $disorders->update();
                            }
                        }
                    }
                    if(isset($_POST['comments_disorder'][$sectionId]) && $_POST['comments_disorder'][$sectionId] != ''
                        && !in_array($sectionId,$existing_comment_ids)) {
                        $disorder_comments = new Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments;
                        $disorder_comments->element_id = $this->id;
                        $disorder_comments->ophcocvi_clinicinfo_disorder_section_id = $sectionId;
                        $disorder_comments->comments = $_POST['comments_disorder'][$sectionId];
                        if (!$disorder_comments->save()) {
                            throw new Exception('Unable to save MultiSelect item: '.print_r($disorder_comments->getErrors(),true));
                        }
                    }
                    else if(isset($_POST['comments_disorder'][$sectionId])){
                        $criteria = new \CDbCriteria;
                        $criteria->compare('element_id', $this->id);
                        $criteria->compare('ophcocvi_clinicinfo_disorder_section_id', $sectionId);
                        $disorder_comments = Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments::model()->find($criteria);
                        $disorder_comments->comments = $_POST['comments_disorder'][$sectionId];
                        $disorder_comments->save();
                    }
                }
            }
        }

        return parent::afterSave();
    }

    /**
     * @return string
     */
    public function getDisplayStatus()
    {
        return $this->is_considered_blind ? static::$BLIND_STATUS : static::$NOT_BLIND_STATUS;
    }

    /**
     * To generate the low vision status array for the pdf
     *
     * @return array
     */
    public function generateFieldOfVision() {
        $data = array();
        $field_of_vision_statuses = (OphCoCvi_ClinicalInfo_FieldOfVision::model()->findAll(array('order' => 'display_order asc')));
        foreach($field_of_vision_statuses as $field_of_vision_status) {
            $key = $field_of_vision_status->name;
            $data[][$key] = ($this->field_of_vision === $field_of_vision_status->id) ? 'X' : '';
        }
        return $data;
    }

    /**
     * To generate the low vision status array for the pdf
     *
     * @return array
     */
    public function generateLowVisionStatus() {
        $data = array();
        $low_vision_statuses = (OphCoCvi_ClinicalInfo_LowVisionStatus::model()->findAll(array('order' => 'display_order asc')));
        foreach($low_vision_statuses as $low_vision_status) {
            $key = $low_vision_status->name;
            $data[][$key] = ($this->low_vision_status_id === $low_vision_status->id) ? 'X' : '';
        }
        return $data;
    }

    /**
     * Returns an associative array of the data values for printing
     */
    public function getStructuredDataForPrint()
    {
        $result = array();
        $result['examinationDate'] = date('d/m/Y', strtotime($this->examination_date));
        $result['isConsideredBlind'] = ($this->is_considered_blind) ? 'Yes' : 'No';
        $result['consultantName'] = $this->consultant->getFullName();
        $result['unaidedRightVA'] = $this->unaided_right_va;
        $result['unaidedLeftVA'] = $this->unaided_left_va;
        $result['bestCorrectedLeftVA'] = $this->best_corrected_left_va;
        $result['bestCorrectedRightVA'] = $this->best_corrected_right_va;
        $result['bestCorrectedBinocularVA'] = $this->best_corrected_binocular_va;
        $result['fieldOfVision'] = $this->generateFieldOfVision();
        $result['lowVisionStatus'] = $this->generateLowVisionStatus();
        return $result;
    }
}