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
 * @property \ElementType $element_type
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property OphCoCvi_ClinicalInfo_LowVisionStatus $low_vision_status
 * @property OphCoCvi_ClinicalInfo_FieldOfVision $field_of_vision
 * @property Element_OphCoCvi_ClinicalInfo_Disorder_Assignment[] $cvi_disorder_assignments
 * @property OphCoCvi_ClinicalInfo_Disorder[] $cvi_disorders
 * @property OphCoCvi_ClinicalInfo_Disorder[] $left_affected_cvi_disorders
 * @property OphCoCvi_ClinicalInfo_Disorder[] $right_affected_cvi_disorders
 * @property Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments[] $cvi_disorder_section_comments
 * @property \User $consultant
 * @property \ProtectedFile $consultant_signature
 */

class Element_OphCoCvi_ClinicalInfo extends \BaseEventTypeElement
{
    public static $BLIND_STATUS = 'Severely Sight Impaired';
    public static $NOT_BLIND_STATUS = 'Sight Impaired';
    public static $NULL_BOOLEAN = '-';
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
                'event_id, examination_date, is_considered_blind, sight_varies_by_light_levels, unaided_right_va, unaided_left_va, best_corrected_right_va, best_corrected_left_va, best_corrected_binocular_va, low_vision_status_id, field_of_vision_id, diagnoses_not_covered, consultant_id, consultant_signature_file_id ',
                'safe'
            ),
            array('examination_date', 'OEDateValidatorNotFuture'),
            array('is_considered_blind', 'boolean'),
            array(
                'unaided_right_va, unaided_left_va, best_corrected_right_va, best_corrected_left_va, best_corrected_binocular_va',
                'length',
                'max' => 20
            ),
            array(
                'examination_date, is_considered_blind, sight_varies_by_light_levels, unaided_right_va, unaided_left_va, best_corrected_right_va, best_corrected_left_va, best_corrected_binocular_va, low_vision_status_id',
                'required',
                'on' => 'finalise'
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
            'cvi_disorder_assignments' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment',
                'element_id'
            ),
            'left_cvi_disorder_assignments' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment',
                'element_id',
                'on' => 'left_cvi_disorder_assignments.eye_id = ' . \Eye::LEFT
            ),
            'right_cvi_disorder_assignments' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment',
                'element_id',
                'on' => 'right_cvi_disorder_assignments.eye_id = ' . \Eye::RIGHT
            ),
            'cvi_disorders' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder',
                'ophcocvi_clinicinfo_disorder_id',
                'through' => 'cvi_disorder_assignments'
            ),
            'left_cvi_disorders' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder',
                'ophcocvi_clinicinfo_disorder_id',
                'through' => 'cvi_disorder_assignments',
                'on' => 'cvi_disorder_assignments.eye_id = ' . \Eye::LEFT
            ),
            'right_cvi_disorders' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder',
                'ophcocvi_clinicinfo_disorder_id',
                'through' => 'cvi_disorder_assignments',
                'on' => 'cvi_disorder_assignments.eye_id = ' . \Eye::RIGHT
            ),
            'left_affected_cvi_disorders' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder',
                'ophcocvi_clinicinfo_disorder_id',
                'through' => 'cvi_disorder_assignments',
                'on' => 'cvi_disorder_assignments.eye_id = ' . \Eye::LEFT . ' AND cvi_disorder_assignments.affected = 1'
            ),
            'right_affected_cvi_disorders' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder',
                'ophcocvi_clinicinfo_disorder_id',
                'through' => 'cvi_disorder_assignments',
                'on' => 'cvi_disorder_assignments.eye_id = ' . \Eye::RIGHT . ' AND cvi_disorder_assignments.affected = 1'
            ),
            'cvi_disorder_section_comments' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments',
                'element_id'
            ),
            'cvi_disorder_sections' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section',
                'section_id',
                'through' => 'cvi_disorders',
                'select' => 'DISTINCT cvi_disorder_sections.*',
                'order' => 'cvi_disorder_sections.display_order asc'
            ),
            'consultant' => array(self::BELONGS_TO, 'User', 'consultant_id'),
            'consultant_signature' => array(self::BELONGS_TO, 'ProtectedFile', 'consultant_signature_file_id'),
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
            'is_considered_blind' => 'I consider this person is',
            'sight_varies_by_light_levels' => 'Sight varies by light levels',
            'unaided_right_va' => 'Unaided right VA',
            'unaided_left_va' => 'Unaided left VA',
            'best_corrected_right_va' => 'Best corrected right VA',
            'best_corrected_left_va' => 'Best corrected left VA',
            'best_corrected_binocular_va' => 'Best corrected binocular VA',
            'low_vision_status_id' => 'Low vision status',
            'field_of_vision_id' => 'Field of vision',
            'disorders' => 'Disorders',
            'diagnoses_not_covered' => 'Diagnosis not covered in any of the above',
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

    /**
     * @TODO: determine encryption/decryption process for the sig file
     * @return mixed
     */
    protected function getDecryptedSignature()
    {
        if ($this->consultant_signature) {
            return file_get_contents($this->consultant_signature->getPath());
        }
    }

    /**
     * @param Element_OphCoCvi_ClinicalInfo_Disorder_Assignment $assignment
     * @param $data
     * @throws \Exception
     */
    private function updateDisorderAssignment(Element_OphCoCvi_ClinicalInfo_Disorder_Assignment $assignment, $data)
    {
        $assignment->element_id = $this->id;
        $assignment->affected = isset($data['affected']) ? (boolean)$data['affected'] : false;
        $assignment->main_cause = isset($data['main_cause']) ? (boolean)$data['main_cause'] : false;

        if (!$assignment->save()) {
            throw new \Exception('Unable to save CVI Disorder Assignment: ' . print_r($assignment->getErrors(), true));
        }
    }

    public function afterValidate()
    {
        foreach (array('left', 'right') as $side) {
            foreach ($this->{$side . '_cvi_disorder_assignments'} as $ass) {
                // would prefer this to be just on the assignment validation, but having trouble
                // with scenario specification for different contexts (i.e. validating for errors to return
                // to the user versus actually saving (where the element id is requred)
                if ($ass->main_cause && !$ass->affected) {
                    $this->addError('disorders', "{$side} disorder - '{$ass->ophcocvi_clinicinfo_disorder->name}'' cannot be main cause when eye not affected.");
                }
            }
        }
        parent::afterValidate();
    }

    /**
     * Update the CVI Disorder status for this element.
     *
     * @param $side
     * @param $data
     * @throws \Exception
     */
    public function updateDisorders($side, $data)
    {
        if (!in_array($side, array('left', 'right'))) {
            throw new \Exception("invalid side specification");
        }

        $eye_id = $side == 'left' ? \Eye::LEFT : \Eye::RIGHT;

        // ensure we're manipulating what is currently in the db
        $current = $this->getRelated($side . "_cvi_disorder_assignments", true);

        // if the element has been saved before, then the assignment values will exist
        // and we can update, or delete those that are no longer required.
        while ($assignment = array_shift($current)) {
            if (array_key_exists($assignment->ophcocvi_clinicinfo_disorder_id, $data)) {
                $this->updateDisorderAssignment($assignment, $data[$assignment->ophcocvi_clinicinfo_disorder_id]);
                unset($data[$assignment->ophcocvi_clinicinfo_disorder_id]);
            } else {
                if (!$assignment->delete()) {
                    throw new \Exception('Unable to delete CVI Disorder Assignment: ' . print_r($assignment->getErrors(),
                            true));
                }
            }
        }

        // create new assignments that don't yet exist for the element
        foreach ($data as $cvi_disorder_id => $values) {
            $assignment = new Element_OphCoCvi_ClinicalInfo_Disorder_Assignment();
            $assignment->eye_id = $eye_id;
            $assignment->ophcocvi_clinicinfo_disorder_id = $cvi_disorder_id;

            $this->updateDisorderAssignment($assignment, $values);
        }
    }


    /**
     * Set the section comments on the element.
     *
     * @param $data
     * @throws \CDbException
     * @throws \Exception
     */
    public function updateDisorderSectionComments($data)
    {
        $current = $this->getRelated('cvi_disorder_section_comments', true);

        while ($section_comment = array_shift($current)) {
            if (array_key_exists($section_comment->ophcocvi_clinicinfo_disorder_section_id, $data)) {
                $comment_data = $data[$section_comment->ophcocvi_clinicinfo_disorder_section_id];
                $section_comment->comments = isset($comment_data['comments']) ? $comment_data['comments'] : "";
                if (!$section_comment->save()) {
                    throw new \Exception('Unable to save CVI Disorder Section Comment: ' . print_r($section_comment->getErrors(),
                            true));
                }
                unset($data[$section_comment->ophcocvi_clinicinfo_disorder_section_id]);
            } else {
                if (!$section_comment->delete()) {
                    throw new \Exception('Unable to delete CVI Disorder Section Comment: ' . print_r($section_comment->getErrors(),
                            true));
                }
            }
        }

        foreach ($data as $section_id => $values) {
            $section_comment = new Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments();
            $section_comment->ophcocvi_clinicinfo_disorder_section_id = $section_id;
            $section_comment->comments = isset($values['comments']) ? $values['comments'] : "";
            $section_comment->element_id = $this->id;
            if (!$section_comment->save()) {
                throw new \Exception("Unable to save CVI Disorder Section Comment: " . print_r($section_comment->getErrors(),
                        true));
            }
        }
    }

    /**
     * @return string
     */
    public function getDisplayStatus()
    {
        $considered_blind = $this->getDisplayConsideredBlind();
        return $considered_blind === static::$NULL_BOOLEAN ? $considered_blind : '';
    }

    /**
     * @return string
     */
    public function getDisplayConsideredBlind()
    {
        if ($this->is_considered_blind === null) {
            return static::$NULL_BOOLEAN;
        } else {
            return $this->is_considered_blind ? static::$BLIND_STATUS : static::$NOT_BLIND_STATUS;
        }
    }

    /**
     * @return string
     */
    public function getDisplayLightLevels()
    {
        if ($this->sight_varies_by_light_levels === null) {
            return static::$NULL_BOOLEAN;
        } else {
            return $this->sight_varies_by_light_levels ? 'Yes' : 'No';
        }
    }

    /**
     * To generate the low vision status array for the pdf
     *
     * @return array
     */
    public function generateFieldOfVision()
    {
        $data = array();
        $field_of_vision_statuses = OphCoCvi_ClinicalInfo_FieldOfVision::model()->findAll(array('order' => 'display_order asc'));
        foreach ($field_of_vision_statuses as $field_of_vision_status) {
            $key = $field_of_vision_status->name;
            $data[] = array($key, ($this->field_of_vision_id === $field_of_vision_status->id) ? 'X' : '');
        }
        return $data;
    }

    /**
     * To generate the low vision status array for the pdf
     *
     * @return array
     */
    public function generateLowVisionStatus()
    {
        $data = array();
        $low_vision_statuses = OphCoCvi_ClinicalInfo_LowVisionStatus::model()->findAll(array('order' => 'display_order asc'));
        foreach ($low_vision_statuses as $low_vision_status) {
            $key = $low_vision_status->name;
            $data[] = array($key, ($this->low_vision_status_id === $low_vision_status->id) ? 'X' : '');
        }
        return $data;
    }

    /**
     * @var array
     */
    private $disorders_by_section;

    /**
     * @return mixed
     */
    private function getAllDisordersFromAssignments()
    {
        $ids = array();
        foreach ($this->left_cvi_disorder_assignments as $ass) {
            $ids[] = $ass->ophcocvi_clinicinfo_disorder_id;
        }
        foreach ($this->right_cvi_disorder_assignments as $ass) {
            $ids[] = $ass->ophcocvi_clinicinfo_disorder_id;
        }
        return OphCoCvi_ClinicalInfo_Disorder::model()->findAllByPk($ids);
    }

    /**
     * @param OphCoCvi_ClinicalInfo_Disorder_Section $section
     * @return mixed
     * @throws \Exception
     */
    protected function getAllDisordersForSection(OphCoCvi_ClinicalInfo_Disorder_Section $section)
    {
        if (!$this->disorders_by_section) {
            $this->disorders_by_section = array();
            $seen = array();

            // assume here that the assignment attributes have been set from the default controller.
            if ($this->isModelDirty()) {
                $cvi_disorders = $this->getAllDisordersFromAssignments();
            } else {
                $cvi_disorders = $this->cvi_disorders;
            }

            foreach ($cvi_disorders as $disorder) {
                if (in_array($disorder->id, $seen)) {
                    continue;
                }
                if (!array_key_exists($disorder->section_id, $this->disorders_by_section)) {
                    $this->disorders_by_section[$disorder->section_id] = array($disorder);
                } else {
                    $this->disorders_by_section[$disorder->section_id][] = $disorder;
                }
                $seen[] = $disorder->id;
            }
        }
        if (!array_key_exists($section->id, $this->disorders_by_section)) {
            return array();
        }
        return $this->disorders_by_section[$section->id];
    }

    protected function getStructuredTextForDisorderSide(OphCoCvi_ClinicalInfo_Disorder $disorder, $side)
    {
        $val = $this->hasCviDisorderForSide($disorder, $side) ? 'X' : '';
        $val .= $this->isCviDisorderMainCauseForSide($disorder, $side) ? ' *' : '';

        return $val;
    }

    /**
     * @param OphCoCvi_ClinicalInfo_Disorder_Section $disorder_section
     * @param int $header_rows - number of empty rows to prepend data with
     * @return array
     * @throws \Exception
     */
    public function getStructuredDisordersForSection(
        OphCoCvi_ClinicalInfo_Disorder_Section $disorder_section,
        $header_rows = 0
    ) {
        $data = array();
        for ($i = 0; $i < $header_rows; $i++) {
            $data[] = array('', '', '', '', '');
        }

        foreach ($this->getAllDisordersForSection($disorder_section) as $i => $disorder) {
            $section_name = '';
            if ($i == 0) {
                $section_name = $disorder_section->name;
            }

            $data[] = array(
                $section_name,
                $disorder->name,
                $disorder->code,
                $this->getStructuredTextForDisorderSide($disorder, 'right'),
                $this->getStructuredTextForDisorderSide($disorder, 'left')
            );
        }

        if ($disorder_section->comments_allowed) {
            $comments_obj = $this->getDisorderSectionComment($disorder_section);
            $text = $disorder_section->comments_label . ' : ';
            if ($comments_obj) {
                $text .= $comments_obj->comments;
            }
            $data[] = array('', $text, '', '', '');
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function generateStructuredBlind()
    {
        return array(
            array('', !$this->is_considered_blind ? 'X' : '', ''),
            array('', $this->is_considered_blind ? 'X' : '', ''),
        );
    }

    /**
     * @return array
     */
    protected function generateStructuredVA()
    {
        return array(
            array('', '', ''),
            array('Unaided', $this->unaided_right_va, $this->unaided_left_va),
            array('Best corrected', $this->best_corrected_right_va, $this->best_corrected_left_va),
            array('Best corrected with both eyes', $this->best_corrected_binocular_va, ''),
        );
    }

    /**
     * @return array
     */
    protected function generateStructuredVisionTable()
    {
        $field_of_vision_data = $this->generateFieldOfVision();
        $low_vision_data = $this->generateLowVisionStatus();
        // mismatch in row count between the two lists.
        $low_vision_data[] = array('', '');

        // empty header row
        $data = array(
            array('', '', '', '')
        );

        for ($k = 0, $k_max = count($field_of_vision_data); $k < $k_max; $k++) {
            $data[$k + 1] = array_merge($field_of_vision_data[$k],
                $low_vision_data[$k]);
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function generateStructuredDisorderTable()
    {
        $data = array();

        foreach ($this->cvi_disorder_sections as $i => $disorder_section) {
            $data['disorder' . ucfirst($disorder_section->name) . 'Table'] =
                $this->getStructuredDisordersForSection($disorder_section, ($i === 0) ? 1 : 0);
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
        $result['isConsideredBlind'] = $this->generateStructuredBlind();
        $result['visualAcuity'] = $this->generateStructuredVA();

        if ($this->consultant) {
            $result['consultantName'] = $this->consultant->getFullName();
        }

        if ($sig_file = $this->getDecryptedSignature()) {
            $result['signatureImageConsultant'] = imagecreatefromstring($sig_file);
        }

        $result['fieldOfVisionAndLowVisionStatus'] = $this->generateStructuredVisionTable();

        $result['sightVariesByLightLevelYes'] = $this->sight_varies_by_light_levels ? 'X' : '';
        $result['sightVariesByLightLevelNo'] = $this->sight_varies_by_light_levels ? '' : 'X';

        $result = array_merge($result, $this->generateStructuredDisorderTable());

        $result['diagnosisNotCovered'] = $this->diagnoses_not_covered;
        return $result;
    }

    /**
     * @param $section
     * @return Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments
     */
    public function getDisorderSectionComment($section)
    {
        foreach ($this->cvi_disorder_section_comments as $comment) {
            if ($section->id == $comment->ophcocvi_clinicinfo_disorder_section_id) {
                return $comment;
            }
        }
    }

    /**
     * @param OphCoCvi_ClinicalInfo_Disorder $cvi_disorder
     * @param string $side left or right
     * @return boolean
     * @throws \Exception
     */
    public function hasCviDisorderForSide(OphCoCvi_ClinicalInfo_Disorder $cvi_disorder, $side)
    {
        if (!in_array($side, array('left', 'right'))) {
            throw new \Exception("invalid side attribute");
        }
        foreach ($this->{$side . '_cvi_disorder_assignments'} as $recorded_cvi) {
            if ($recorded_cvi->ophcocvi_clinicinfo_disorder_id == $cvi_disorder->id) {
                return $recorded_cvi->affected;
            }
        }
    }

    /**
     * @param OphCoCvi_ClinicalInfo_Disorder $cvi_disorder
     * @return bool
     * @throws \Exception
     */
    public function hasAffectedCviDisorderInSection(OphCoCvi_ClinicalInfo_Disorder_Section $cvi_disorder_section)
    {
        foreach ($this->getAllDisordersForSection($cvi_disorder_section) as $cvi_disorder) {
            if ($this->hasCviDisorderForSide($cvi_disorder, 'right')) {
                return true;
            }
            if ($this->hasCviDisorderForSide($cvi_disorder, 'left')) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param OphCoCvi_ClinicalInfo_Disorder $cvi_disorder
     * @param $side
     * @return mixed
     * @throws \Exception
     */
    public function isCviDisorderMainCauseForSide(OphCoCvi_ClinicalInfo_Disorder $cvi_disorder, $side)
    {
        if (!in_array($side, array('left', 'right'))) {
            throw new \Exception("invalid side attribute");
        }
        foreach ($this->{$side . '_cvi_disorder_assignments'} as $recorded_cvi) {
            if ($recorded_cvi->ophcocvi_clinicinfo_disorder_id == $cvi_disorder->id) {
                return $recorded_cvi->main_cause;
            }
        }
    }

    /**
     * @return bool
     */
    public function isSigned()
    {
        return $this->consultant_signature_file_id ? true : false;
    }
}
