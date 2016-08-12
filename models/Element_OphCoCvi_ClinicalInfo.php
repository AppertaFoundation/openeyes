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
                foreach ($_POST['ophcocvi_clinicinfo_disorder_section_id_'.$side] as $sectionId) {
                    foreach ($_POST['ophcocvi_clinicinfo_disorder_id_'.$side] as $id) {
                        if($_POST['affected_'.$side][$id] == 1) {
                            $item = new Element_OphCoCvi_ClinicalInfo_Disorder_Assignment;
                            $item->element_id = $this->id;
                            $item->eye_id = $side_value;
                            $item->ophcocvi_clinicinfo_disorder_id = $id;
                            $item->affected = $_POST['affected_'.$side][$id];
                            $item->main_cause = isset($_POST['main_cause_'.$side][$id]) ? $_POST['main_cause_'.$side][$id] : 0;
                            if (!$item->save()) {
                                throw new Exception('Unable to save MultiSelect item: '.print_r($item->getErrors(),true));
                            }
                        }
                    }
                    if(isset($_POST['comments_'.$side][$sectionId])) {
                        $item = new Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments;
                        $item->element_id = $this->id;
                        $item->eye_id = $side_value;
                        $item->ophcocvi_clinicinfo_disorder_section_id = $sectionId;
                        $item->comments = $_POST['comments_'.$side][$sectionId];
                        if (!$item->save()) {
                            throw new Exception('Unable to save MultiSelect item: '.print_r($item->getErrors(),true));
                        }
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
     * Returns an associative array of the data values for printing
     */
    public function getStructuredDataForPrint()
    {
        $result = array();
        $result['examinationDate'] = date('d/m/Y', strtotime($this->examination_date));
        $result['is_considered_blind'] = ($this->is_considered_blind) ? 'Yes' : 'No';

        $result['consultantName'] = $this->consultant->getFullName();
        //var_dump(\Institution::model()->getCurrent());exit;
        //var_dump($consultant_name);exit;

        return $result;
    }
}