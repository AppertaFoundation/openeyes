<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
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
 * @property integer $information_booklet
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
 * @property  $main_cause_pdf_id ID radio button of in original cvi pdf - see more: pdtfk->getDataFields()
 */

use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered;

class Element_OphCoCvi_ClinicalInfo_V1 extends \BaseEventTypeElement
{

    public static $BLIND_STATUS = 'Severely Sight Impaired';
    public static $NOT_BLIND_STATUS = 'Sight Impaired';
    public static $NULL_BOOLEAN = 'Not recorded';
    const CVI_TYPE_ADULT = 0;
    const CVI_TYPE_CHILD = 1;
    const VISUAL_ACUITY_TYPE_SNELLEN = 1;
    const VISUAL_ACUITY_TYPE_LOGMAR = 2;

    private $mainCause = "Off";

    /*
     * This variables needs to the CVI Manager
     */
    public $centralVisualPathwayProblemsQuiestionID = 0;
    public $anophtalmosMicrophthalmosQuiestionID = 0;
    public $disorganisedGlobePhthisisQuiestionID = 0;
    public $primaryCongenitalInfantileGlaucomaQuiestionID = 0;

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
            array(//low_vision_status_id?
                'best_recorded_right_va,best_recorded_left_va,best_recorded_binocular_va,patient_type,field_of_vision,low_vision_service,best_corrected_left_va_list,best_corrected_right_va_list,best_corrected_binocular_va_list, event_id, examination_date, is_considered_blind, information_booklet, eclo, best_corrected_right_va, best_corrected_left_va, best_corrected_binocular_va,field_of_vision_id, diagnoses_not_covered, consultant_id, consultant_signature_file_id, main_cause_pdf_id ',
                'safe'
            ),
            array('examination_date', 'OEDateValidatorNotFuture'),
            array('patient_type,is_considered_blind', 'boolean'),
            array(
                'best_corrected_right_va, best_corrected_left_va, best_corrected_binocular_va',
                'length',
                'max' => 20
            ),
            array( //low_vision_status_id?
                'patient_type,examination_date, is_considered_blind, information_booklet, eclo, best_corrected_right_va, best_corrected_left_va, best_corrected_binocular_va, best_corrected_right_va_list',
                'required',
                'on' => 'finalise'
            ),
            array(//low_vision_status_id?
                'id, patient_type,event_id, examination_date, is_considered_blind, information_booklet, eclo, best_corrected_right_va, best_corrected_left_va, best_corrected_binocular_va, field_of_vision_id, diagnoses_not_covered, consultant_id, ',
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
            'field_of_vision' => array(
                self::BELONGS_TO,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_FieldOfVision',
                'field_of_vision_id'
            ),
            'diagnosis_not_covered' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered',
                'element_id'
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
            'main_cause_cvi_disorder_assignment' => array(
                self::HAS_ONE,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment',
                'element_id',
                'on' => 'main_cause_cvi_disorder_assignment.eye_id = ' . \Eye::RIGHT . ' AND main_cause_cvi_disorder_assignment.main_cause = 1'
            ),
            'both_cvi_disorder_assignments' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment',
                'element_id',
                'on' => 'both_cvi_disorder_assignments.eye_id = ' . \Eye::BOTH
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
            'information_booklet' => 'I have made the patient aware of the information booklet, "Sight loss: What we needed to know"',
            'eclo' => 'Has the patient seen an Eye Clinic Liaison Officer (ECLO) / Sight Loss Advisor?',
            'unaided_right_va' => 'Unaided right VA',
            'unaided_left_va' => 'Unaided left VA',
            'best_corrected_right_va' => 'Best corrected right VA',
            'best_corrected_left_va' => 'Best corrected left VA',
            'best_corrected_binocular_va' => 'Best corrected binocular VA (Habitual)',
            'best_recorded_right_va' => 'Best recorded right VA',
            'best_recorded_left_va' => 'Best recorded left VA',
            'best_recorded_binocular_va' => 'Best recorded binocular VA',
            'low_vision_service' => 'Low vision service: <br> If appropriate, has a referal for the low vision service been made?',
            'field_of_vision' => 'Field of vision: Extensive loss of peripheral visual field (including hemianopia)',
            'field_of_vision_id' => 'Field of vision',
            'disorders' => 'Disorders',
            'diagnoses_not_covered' => 'Diagnosis not covered in any of the above',
            'consultant_id' => 'Consultant',
            'best_corrected_right_va_list' => 'Scale',
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
        $criteria->compare('information_booklet', $this->eclo);
        $criteria->compare('eclo', $this->eclo);
        $criteria->compare('best_corrected_right_va', $this->best_corrected_right_va);
        $criteria->compare('best_corrected_left_va', $this->best_corrected_left_va);
        $criteria->compare('best_corrected_binocular_va', $this->best_corrected_binocular_va);
        $criteria->compare('patient_type', $this->patient_type);
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
        if ($this->getScenario() == 'finalise') {
            if (empty($this->cvi_disorder_assignments) && empty($this->diagnosis_not_covered)) {
                $this->addError('disorders', "Please select a diagnosis or add a diagnosis to the diagnosis not covered list");
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
        if (!in_array($side, array('left', 'right', 'both'))) {
            throw new \Exception("invalid side specification");
        }

        switch ($side) {
            case 'left':
                $eye_id = \Eye::LEFT;
                break;
            case 'right':
                $eye_id = \Eye::RIGHT;
                break;
            case 'both':
                $eye_id = \Eye::BOTH;
                break;
        }

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
     * Update the CVI Disorder status for this element.
     *
     * @param $side
     * @param $data
     * @throws \Exception
     */
    public function updateDisordersNotCovered($data)
    {
        foreach ($data as $key => $disorder) {
            $diagnosis = new OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered();
            $diagnosis->element_id = $this->id;
            $diagnosis->disorder_id = isset($disorder['disorder_id']) ? $disorder['disorder_id'] : false;
            $diagnosis->eye_id = isset($disorder['eyes']) ? $disorder['eyes'] : false;
            $diagnosis->main_cause = isset($disorder['main_cause']) ? $disorder['main_cause'] : false;
            $diagnosis->disorder_type = isset($disorder['disorder_type']) ? $disorder['disorder_type'] : false;
            if ($diagnosis->disorder_type == OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered::TYPE_DISORDER) {
                $diagnosis->code = isset($disorder['code']) ? $disorder['code'] : false;
            }

            if (!$diagnosis->save()) {
                throw new \Exception('Unable to save CVI Diagnosis not covered: ' . print_r($diagnosis->getErrors(), true));
            }
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
        foreach ($this->cvi_disorder_assignments as $ass) {
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
        if (!in_array($side, array('left', 'right', 'both'))) {
            throw new \Exception("invalid side attribute");
        }
        foreach ($this->{$side . '_cvi_disorder_assignments'} as $recorded_cvi) {
            if ($recorded_cvi->ophcocvi_clinicinfo_disorder_id == $cvi_disorder->id) {
                return $recorded_cvi->affected;
            }
        }
    }

    /**
     * Returns the eye seide for disorder
     *
     * @param OphCoCvi_ClinicalInfo_Disorder $cvi_disorder
     * @return int
     */
    public function getCviDisorderSide(OphCoCvi_ClinicalInfo_Disorder $cvi_disorder): int
    {
        $left = false;
        $right = false;

        foreach (['left', 'right'] as $side) {
            foreach ($this->{$side . '_cvi_disorder_assignments'} as $recorded_cvi) {
                if ($recorded_cvi->ophcocvi_clinicinfo_disorder_id == $cvi_disorder->id) {
                    $$side = $recorded_cvi->affected;
                }
            }
        }

        if ($left && $right) {
            return \Eye::BOTH;
        }

        return $left ? \Eye::LEFT : ($right ? \Eye::RIGHT : 0);
    }

    /**
     * @param OphCoCvi_ClinicalInfo_Disorder $cvi_disorder
     * @param string $side left or right
     * @return boolean
     * @throws \Exception
     */
    public function hasCviDisorderForAny(OphCoCvi_ClinicalInfo_Disorder $cvi_disorder)
    {
        foreach ($this->cvi_disorder_assignments as $recorded_cvi) {
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
            if ($this->hasCviDisorderForSide($cvi_disorder, 'both')) {
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
        if (!in_array($side, array('right'))) {
            throw new \Exception("invalid side attribute");
        }

        foreach ($this->{$side . '_cvi_disorder_assignments'} as $recorded_cvi) {
            if ($recorded_cvi->ophcocvi_clinicinfo_disorder_id == $cvi_disorder->id) {
                return $recorded_cvi->main_cause;
            }
        }
    }

    public function isCviDisorderMainCauseForAny(OphCoCvi_ClinicalInfo_Disorder $cvi_disorder, $side)
    {
        if (!empty($this->main_cause_cvi_disorder_assignment)) {
            foreach ($this->right_cvi_disorder_assignments as $recorded_cvi) {
                if ($recorded_cvi->ophcocvi_clinicinfo_disorder_id == $cvi_disorder->id) {
                    return $recorded_cvi->main_cause;
                }
            }
        } else {
            foreach ($this->cvi_disorder_assignments as $recorded_cvi) {
                if ($recorded_cvi->ophcocvi_clinicinfo_disorder_id == $cvi_disorder->id) {
                    return $recorded_cvi->affected;
                }
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


    public function getInformationBooklets()
    {
        $information_booklets = [
            1 => 'Yes',
            0 => 'No',
        ];

        return $information_booklets;
    }

    public function getDisplayInformationBooklet()
    {
        if ($this->information_booklet === null) {
            return '';
        } else {
            return $this->getInformationBooklets()[$this->information_booklet];
        }
    }

    public function getEclo()
    {
        $eclo = [
            1 => 'Yes',
            2 => 'Referred',
            0 => 'Not available',
        ];

        return $eclo;
    }

    public function getDisplayEclo()
    {
        if ($this->eclo === null) {
            return '';
        } else {
            return $this->getEclo()[$this->eclo];
        }
    }


    public function getBestCorrectedVAList()
    {
        $corrected = [
            self::VISUAL_ACUITY_TYPE_SNELLEN => 'Snellen',
            self::VISUAL_ACUITY_TYPE_LOGMAR => 'Logmar',
        ];

        return $corrected;
    }

    public function getDisplayBestCorrectedRightVAList()
    {
        if ($this->best_corrected_right_va_list === null || $this->best_corrected_right_va_list == 0) {
            return 'Not recorded';
        } else {
            return $this->getBestCorrectedVAList()[$this->best_corrected_right_va_list];
        }
    }

    public function getDisplayBestCorrectedRightVA()
    {
        if ($this->best_corrected_right_va === null || $this->best_corrected_right_va == '') {
            return '';
        } else {
            if ($this->best_corrected_right_va_list == self::VISUAL_ACUITY_TYPE_SNELLEN) {
                return ', '.self::getSnellenDatas()[$this->best_corrected_right_va];
            } elseif ($this->best_corrected_right_va_list == self::VISUAL_ACUITY_TYPE_LOGMAR) {
                return ', '.self::getLogmarDatas()[$this->best_corrected_right_va];
            }
        }
    }

    public function getDisplayBestCorrectedLeftVA()
    {
        if ($this->best_corrected_left_va === null || $this->best_corrected_left_va == '') {
            return 'Not recorded';
        } else {
            if ($this->best_corrected_right_va_list == self::VISUAL_ACUITY_TYPE_SNELLEN) {
                return ', '.self::getSnellenDatas()[$this->best_corrected_left_va];
            } elseif ($this->best_corrected_right_va_list == self::VISUAL_ACUITY_TYPE_LOGMAR) {
                return ', '.self::getLogmarDatas()[$this->best_corrected_left_va];
            }
        }
    }

    public function getDisplayBestCorrectedBinocularVA()
    {
        if ($this->best_corrected_binocular_va === null || $this->best_corrected_binocular_va == '') {
            return 'Not recorded';
        } else {
            if ($this->best_corrected_right_va_list == self::VISUAL_ACUITY_TYPE_SNELLEN) {
                return ', '.self::getSnellenDatas()[$this->best_corrected_binocular_va];
            } elseif ($this->best_corrected_right_va_list == self::VISUAL_ACUITY_TYPE_LOGMAR) {
                return ', '.self::getLogmarDatas()[$this->best_corrected_binocular_va];
            }
        }
    }

    public function getDisplayBestCorrectedLeftVAList()
    {
        if ($this->best_corrected_right_va_list === null || $this->best_corrected_right_va_list == 0) {
            return '';
        } else {
            return $this->getBestCorrectedVAList()[$this->best_corrected_right_va_list];
        }
    }
    public function getDisplayBestCorrectedBinocularVAList()
    {
        if ($this->best_corrected_right_va_list === null || $this->best_corrected_right_va_list == 0) {
            return '';
        } else {
            return $this->getBestCorrectedVAList()[$this->best_corrected_right_va_list];
        }
    }

    public function getFieldOfVision()
    {
        $field_of_vision = [
            1 => 'Yes',
            2 => 'No',
        ];


        return $field_of_vision;
    }

    public function getDisplayFieldOfVision()
    {
        if ($this->field_of_vision === null) {
            return '';
        } else {
            return $this->getFieldOfVision()[$this->field_of_vision];
        }
    }

    public function getLowVisionService()
    {
        $low_vision_service = [
            1 => "Yes",
            2 => "No",
            3 => "Don't know",
            4 => "Not Required"
        ];

        return $low_vision_service;
    }

    public function getDisplayLowVisionService()
    {
        if ($this->low_vision_service === null) {
            return '';
        } elseif (isset($this->getLowVisionService()[$this->low_vision_service])) {
            return $this->getLowVisionService()[$this->low_vision_service];
        } else {
            return '';
        }
    }

    public function getLogmarDatas()
    {
        return [
            'NPL',
            'PL',
            'HM',
            'CF',
            '2.0',
            '1.8',
            '1.6',
            '1.4',
            '1.2',
            '1.0',
            '0.9',
            '0.8',
            '0.7',
            '0.6',
            '0.5',
            '0.4',
            '0.3',
            '0.2',
            '0.1',
            '0.0',
            '-0.1',
            '-0.2',
            'FF',
            'UA',
            'AE'
        ];
    }

    public function getSnellenDatas()
    {
        return [
            'NPL',
            'PL',
            'HM',
            'CF',
            '1/60',
            '2/60',
            '3/60',
            '6/60',
            '6/36',
            '6/24',
            '6/18',
            '6/12',
            '6/9',
            '6/6',
            '6/5',
            '6/4',
            'FF',
            'UA',
            'AE'
        ];
    }

    public function getBestCorrectedVAForPdf($eye, $type)
    {
        $result = '';
        $best_corrected_va_list = 'best_corrected_right_va_list';
        $best_corrected_va = 'best_corrected_'.$eye.'_va';

        if ($this->{$best_corrected_va_list} == $type) {
            if (!is_null($this->{$best_corrected_va}) && $this->{$best_corrected_va} !== null) {
                if ($type == self::VISUAL_ACUITY_TYPE_LOGMAR) {
                    $result = self::getLogmarDatas()[$this->{$best_corrected_va}];
                } elseif ($type == self::VISUAL_ACUITY_TYPE_SNELLEN) {
                    $result = self::getSnellenDatas()[$this->{$best_corrected_va}];
                }
            }
        } else {
            return '';
        }
        return $result;
    }

    /*
     * Get elements for CVI PDF
     *
     * @return array
     */
    public function getElementsForCVIpdf()
    {

        $elements = [
            'Opthalm1'   => $this->is_considered_blind,
            'I have made the patient aware of the information booklet, “Sight Loss: What we needed to know”' => ($this->information_booklet == 0) ? 1 : 0,
            'information_booklet_string' => ($this->information_booklet == 0) ? "No" : "Yes",
            'Has the patient seen an Eye Clinic Liaison Officer (ECLO)/Sight Loss Advisor?' => $this->getEcloForPDF(),
            'sight_loss_advisior' => $this->getEcloForVisualyImpaired(),
            'Examination_date' => \Helper::convertMySQL2NHS($this->examination_date),
            'Right eye: Logmar'     => $this->getBestCorrectedVAForPdf('right', self::VISUAL_ACUITY_TYPE_LOGMAR), //Values: -0.1, -0.2, 0.0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0, 1.2, 1.4, 1.6, 1.8., 2.0, "AE", "CF", "FF", "HM", "NPL", "PL", "UA"
            'Left eye: Logmar'      => $this->getBestCorrectedVAForPdf('left', self::VISUAL_ACUITY_TYPE_LOGMAR), //Values: -0.1, -0.2, 0.0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0, 1.2, 1.4, 1.6, 1.8., 2.0, "AE", "CF", "FF", "HM", "NPL", "PL", "UA"
            'Binocular: Logmar'     => $this->getBestCorrectedVAForPdf('binocular', self::VISUAL_ACUITY_TYPE_LOGMAR), //Values: -0.1, -0.2, 0.0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0, 1.2, 1.4, 1.6, 1.8., 2.0, "AE", "CF", "FF", "HM", "NPL", "PL", "UA"
            'Right eye: Snellen'    => $this->getBestCorrectedVAForPdf('right', self::VISUAL_ACUITY_TYPE_SNELLEN), //Values: 1/60, 2/60,3/60, 6/12, 6/18, 6/24, 6/36, 6/4, 6/5, 6/6, 6/60, 6/9, "AE", "CF", "FF", "HM", "NPL", "PL", "UA"
            'Left eye: Snellen'     => $this->getBestCorrectedVAForPdf('left', self::VISUAL_ACUITY_TYPE_SNELLEN), //Values: 1/60, 2/60,3/60, 6/12, 6/18, 6/24, 6/36, 6/4, 6/5, 6/6, 6/60, 6/9, "AE", "CF", "FF", "HM", "NPL", "PL", "UA"
            'Binocular: Snellen'    => $this->getBestCorrectedVAForPdf('binocular', self::VISUAL_ACUITY_TYPE_SNELLEN), //Values: 1/60, 2/60,3/60, 6/12, 6/18, 6/24, 6/36, 6/4, 6/5, 6/6, 6/60, 6/9, "AE", "CF", "FF", "HM", "NPL", "PL", "UA"
            'Extensive loss of peripheral visual field (including hemianopia)' => $this->getFieldOfvisionForPDF(),
            'If appropriate, has a referral for the low vision service been made?' => $this->getLowVisionServiceForPDF(), //Values: 0,1,2,3
            'low_vision_service' => $this->getLowVisionServiceForVisualyImpaired(),
            'patient_type' => $this->patient_type,
            'diagnosis_for_visualy_impaired' => $this->generateDiagnosisForVisualyImpaired(),
        ];

        $patientDiagnosis = $this->getPatientDiagnosisForPDF();

        return array_merge( $elements, $patientDiagnosis);
    }

    /**
     * Set diagnosis result by patient age
     * @return array
     */
    private function getPatientDiagnosisForPDF()
    {
        //If patient over 18
        if ($this->patient_type == 0) {
            return [
                'age-related macular degeneration – choroidal neovascularisation (wet): Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 23),
                'age-related macular degeneration – choroidal neovascularisation (wet): Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 23),
                'age-related macular degeneration – atrophic/geographic macular atrophy (dry): Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 24),
                'age-related macular degeneration – atrophic/geographic macular atrophy (dry): Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 24),
                'age-related macular degeneration unspecified (mixed): Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 25),
                'age-related macular degeneration unspecified (mixed): Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 25),
                'diabetic retinopathy: Right eye'   => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 26),
                'diabetic retinopathy: Left eye'    => $this->getDisorderAnswerForPDF( \Eye::LEFT, 26),
                'diabetic maculopathy: Right eye'   => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 27),
                'diabetic maculopathy: Left eye'    => $this->getDisorderAnswerForPDF( \Eye::LEFT, 27),
                'hereditary retinal dystrophy: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 28),
                'hereditary retinal dystrophy: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 28),
                'retinal vascular occlusions: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 29),
                'retinal vascular occlusions: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 29),
                'other retinal (specify): Right eye' => '',
                'other retinal (specify): Left eye' => '',
                'Glaucoma - primary open angle: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 30),
                'Glaucoma - primary open angle: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 30),
                'Glaucoma - primary angle closure: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 31),
                'Glaucoma - primary angle closure: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 31),
                'Glaucoma - secondary: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 32),
                'Glaucoma - secondary: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 32),
                'Glaucoma - other glaucoma: Right eye' => '',
                'Glaucoma - other glaucoma: Left eye' => '',
                'Globe - degenerative myopia: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 33),
                'Globe - degenerative myopia: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 33),
                'Neurological - optic atrophy: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 34),
                'Neurological - optic atrophy: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 34),
                'Neurological - visual cortex disorder: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 35),
                'Neurological - visual cortex disorder: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 35),
                'Neurological - cerebrovascular disease: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 36),
                'Neurological - cerebrovascular disease: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 36),
                'Choroid - chorioretinitis: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 37),
                'Choroid - chorioretinitis: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 37),
                'Choroid - choroidal degeneration: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 38),
                'Choroid - choroidal degeneration: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 38),
                'Lens - cataract (excludes congenital): Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 39),
                'Lens - cataract (excludes congenital): Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 39),
                'Cornea - corneal scars and opacities: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 40),
                'Cornea - corneal scars and opacities: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 40),
                'Cornea - keratitis: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 41),
                'Cornea - keratitis: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 41),
                'Neoplasia - eye: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 42),
                'Neoplasia - eye: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 42),
                'Neoplasia - brain & CNS: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 43),
                'Neoplasia - brain & CNS: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 43),
                'Neoplasia - other neoplasia: Right eye' => '',
                'Neoplasia - other neoplasia: Left eye' => '',
                'Page_2_Diagnosis_Text_Box' => $this->getNotCoveredDiagnosesForPDF(),
                'Main Cause'            => $this->mainCause  //Values: 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,"Off", "Yes"
            ];
        } else {
            return [
                //If patient under 18
                'Central Visual Pathway Problems - cerebral/cortical pathology: Right eye' => $this->getCentralVisualPathway( \Eye::RIGHT ),
                'Central Visual Pathway Problems - cerebral/cortical pathology: Left eye' => $this->getCentralVisualPathway( \Eye::LEFT ),
                'SelectedVisualPathwayProblem' => $this->centralVisualPathwayProblemsQuiestionID,

                'Central Visual Pathway Problems - nystagmus: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 47),
                'Central Visual Pathway Problems - nystagmus: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 47),
                'Central Visual Pathway Problems - other: Right eye' => '',
                'Central Visual Pathway Problems - other: Left eye' => '',

                'Whole Globe and Anterior Segment - anophthalmos/microphthalmos: Right eye' => $this->getAnophthalmosMicrophthalmos( \Eye::RIGHT ),
                'Whole Globe and Anterior Segment - anophthalmos/microphthalmos: Left eye' => $this->getAnophthalmosMicrophthalmos( \Eye::LEFT ),
                'SelectedAnophthalmosMicrophthalmos' => $this->anophtalmosMicrophthalmosQuiestionID,

                'Whole Globe and Anterior Segment - disorganised globe/phthisis: Right eye' =>  $this->getGlobePhthisis( \Eye::RIGHT ),
                'Whole Globe and Anterior Segment - disorganised globe/phthisis: Left eye' =>  $this->getGlobePhthisis( \Eye::LEFT ),
                'SelectedDisorganisedglobePhthisis' => $this->disorganisedGlobePhthisisQuiestionID,

                'Whole Globe and Anterior Segment - anterior segment anomaly: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 52),
                'Whole Globe and Anterior Segment - anterior segment anomaly: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 52),
                'Whole Globe and Anterior Segment - primary congenital/infantile glaucoma: Right eye' =>  $this->getCongenitalInfantile( \Eye::RIGHT ),
                'Whole Globe and Anterior Segment - primary congenital/infantile glaucoma: Left eye' =>  $this->getCongenitalInfantile( \Eye::LEFT ),
                'SelectedPrimaryCongenitalInfantileGlaucoma' => $this->primaryCongenitalInfantileGlaucomaQuiestionID,

                'Whole Globe and Anterior Segment - other glaucoma: Right eye' =>  '',
                'Whole Globe and Anterior Segment - other glaucoma: Left eye' =>  '',
                'Amblyopia - stimulus deprivation: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 55),
                'Amblyopia - stimulus deprivation: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 55),
                'Amblyopia - strabismic: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 56),
                'Amblyopia - strabismic: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 56),
                'Amblyopia - refractive: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 57),
                'Amblyopia - refractive: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 57),
                'Cornea - opacity: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 58),
                'Cornea - opacity: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 58),
                'Cornea - dystrophy: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 59),
                'Cornea - dystrophy: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 59),
                'Cornea - other: Right eye' =>  '',
                'Cornea - other: Left eye' =>  '',
                'Cataract - congenital: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 60),
                'Cataract - congenital: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 60),
                'Cataract - developmental: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 61),
                'Cataract - developmental: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 61),
                'Cataract - secondary: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 62),
                'Cataract - secondary: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 62),
                'Uvea - aniridia: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 63),
                'Uvea - aniridia: Left eye' =>   $this->getDisorderAnswerForPDF( \Eye::LEFT, 63),
                'Uvea - coloboma: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 64),
                'Uvea - coloboma: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 64),
                'Uvea - uveitis: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 65),
                'Uvea - uveitis: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 65),
                'Uvea - other: Right eye' =>  '',
                'Uvea - other: Left eye' =>  '',
                'Retina - retinopathy of prematurity: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 66),
                'Retina - retinopathy of prematurity: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 66),
                'Retina - retinal dystrophy: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 67),
                'Retina - retinal dystrophy: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 67),
                'Retina - retinitis: Right eye' =>  $this->getDisorderAnswerForPDF( \Eye::RIGHT, 68),
                'Retina - retinitis: Left eye' =>  $this->getDisorderAnswerForPDF( \Eye::LEFT, 68),
                'Retina - other retinopathy: Right eye' =>  '',
                'Retina - other retinopathy: Left eye' => '',
                'Retina - retinoblastoma: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 69),
                'Retina - retinoblastoma: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 69),
                'Retina - albinism: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 70),
                'Retina - albinism: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 70),
                'Retina - retinal detachment: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 71),
                'Retina - retinal detachment: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 71),
                'Retina - other: Right eye' => '',
                'Retina - other: Left eye' => '',
                'Optic Nerve - hypoplasia: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 72),
                'Optic Nerve - hypoplasia: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 72),
                'Optic Nerve - other congenital anomaly: Right eye' => '',
                'Optic Nerve - other congenital anomaly: Left eye' => '',
                'Optic Nerve - optic atrophy: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 73),
                'Optic Nerve - optic atrophy: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 73),
                'Optic Nerve - neuropathy: Right eye' => $this->getDisorderAnswerForPDF( \Eye::RIGHT, 74),
                'Optic Nerve - neuropathy: Left eye' => $this->getDisorderAnswerForPDF( \Eye::LEFT, 74),
                'Optic Nerve - other: Right eye' => '',
                'Optic Nerve - other: Left eye' => '',
                'Page_3_Diagnosis_Text_Box 2' => $this->getNotCoveredDiagnosesForPDF(),
                'Main Cause Part 2b' => $this->mainCause         //Values: 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,"Off", "Yes"
            ];
        }
    }

    /**
     * Set diagnosis radio buttons by disorder id
     * @param type $eyeID
     * @param type $questionID
     * @return string
     */
    private function getDisorderAnswerForPDF($eyeID, $questionID)
    {
        $result = "Off";

        switch ( $eyeID ) {
            case 1: //left
                if ($this->left_cvi_disorder_assignments) {
                    foreach ( $this->left_cvi_disorder_assignments as $assignment) {
                        if ($assignment->ophcocvi_clinicinfo_disorder_id == $questionID) {
                            if ($assignment->affected == 1) {
                                $result = "Yes";
                            }

                            if ( $assignment->main_cause > 0) {
                                $this->mainCause = $assignment->ophcocvi_clinicinfo_disorder->main_cause_pdf_id;
                            }
                        }
                    }
                }

                if ($this->both_cvi_disorder_assignments) {
                    foreach ( $this->both_cvi_disorder_assignments as $assignment) {
                        if ($assignment->ophcocvi_clinicinfo_disorder_id == $questionID) {
                            if ($assignment->main_cause > 0) {
                                $this->mainCause = $assignment->ophcocvi_clinicinfo_disorder->main_cause_pdf_id;
                            }

                            if ($assignment->affected == 1) {
                                $result = "Yes";
                            }
                        }
                    }
                }

                break;
            case 2:
                if ($this->right_cvi_disorder_assignments) {
                    foreach ( $this->right_cvi_disorder_assignments as $assignment) {
                        if ($assignment->ophcocvi_clinicinfo_disorder_id == $questionID) {
                            if ($assignment->affected == 1) {
                                $result = "Yes";
                            }

                            if ( $assignment->main_cause > 0) {
                                $this->mainCause = $assignment->ophcocvi_clinicinfo_disorder->main_cause_pdf_id;
                            }
                        }
                    }
                }

                if ($this->both_cvi_disorder_assignments) {
                    foreach ( $this->both_cvi_disorder_assignments as $assignment) {
                        if ($assignment->ophcocvi_clinicinfo_disorder_id == $questionID) {
                            if ($assignment->affected == 1) {
                                $result = "Yes";
                            }

                            if ( $assignment->main_cause > 0) {
                                $this->mainCause = $assignment->ophcocvi_clinicinfo_disorder->main_cause_pdf_id;
                            }
                        }
                    }
                }
                break;
        }

        return $result;
    }

    /**
     * Set selected Central Visual pathway problems from group
     * @param type $eyeSide
     * @return string
     */
    private function getCentralVisualPathway($eyeSide)
    {
        $questionIDs = [44,45,46];
        foreach ($questionIDs as $question) {
            if ($this->getDisorderAnswerForPDF( $eyeSide, $question) == "Yes") {
                $this->centralVisualPathwayProblemsQuiestionID = $question;
                return "Yes";
            }
        }

        return "Off";
    }

    /**
     * Set selected Anophthalmos or Microphthalmos
     * @param type $eyeSide
     * @return string
     */
    private function getAnophthalmosMicrophthalmos($eyeSide)
    {
        $questionIDs = [48,49];
        foreach ($questionIDs as $question) {
            if ($this->getDisorderAnswerForPDF( $eyeSide, $question) == "Yes") {
                $this->anophtalmosMicrophthalmosQuiestionID = $question;
                return "Yes";
            }
        }

        return "Off";
    }

    /**
     * Set selected Disorganised globe or Phthisis
     * @param type $eyeSide
     * @return string
     */
    private function getGlobePhthisis($eyeSide)
    {
        $questionIDs = [50,51];
        foreach ($questionIDs as $question) {
            if ($this->getDisorderAnswerForPDF( $eyeSide, $question) == "Yes") {
                $this->disorganisedGlobePhthisisQuiestionID = $question;
                return "Yes";
            }
        }

        return "Off";
    }

     /**
     * Set selected Primary Congenital or Infantile Glaucoma
     * @param type $eyeSide
     * @return string
     */
    private function getCongenitalInfantile($eyeSide)
    {
        $questionIDs = [53,54];
        foreach ($questionIDs as $question) {
            if ($this->getDisorderAnswerForPDF( $eyeSide, $question) == "Yes") {
                $this->primaryCongenitalInfantileGlaucomaQuiestionID = $question;
                return "Yes";
            }
        }

        return "Off";
    }

    /**
     * Set vision id for pdf
     * @return string
     */
    private function getLowVisionServiceForPDF()
    {

        if ($this->low_vision_service) {
            switch ($this->low_vision_service) {
                case "1":
                    return "3";
                    break;
                case "2":
                    return "0";
                    break;
                case "3":
                    return "1";
                    break;
                case "4":
                    return "2";
                    break;
            }
        }

        return "";
    }

    /**
     *
     * @return string
     */
    private function getLowVisionServiceForVisualyImpaired()
    {
        if ($this->low_vision_service) {
            switch ($this->low_vision_service) {
                case "1":
                    return "Yes";
                    break;
                case "2":
                    return "No";
                    break;
                case "3":
                    return "Don't know";
                    break;
                case "4":
                    return "Not Required";
                    break;
            }
        }

        return "";
    }

    /**
     * Get "Diagnosis not covered text"
     * @return string
     */
    private function getNotCoveredDiagnosesForPDF()
    {
        $result = '';
        if ($this->diagnosis_not_covered) {
            foreach ($this->diagnosis_not_covered as $element) {
                $mainCause = ($element->main_cause == 1) ? '(main cause)' : '';
                switch ($element->eye_id) {
                    case 1:
                        $eye = 'Left';
                        break;
                    case 2:
                        $eye = 'Right';
                        break;
                    default:
                        $eye = 'Both';
                }
                if ($element->disorder_type == OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered::TYPE_CLINICINFO_DISORDER) {
                    $disorder_name = $element->clinicinfo_disorder->term_to_display;
                    $disorder_code = $element->clinicinfo_disorder->code;
                } else {
                    $disorder_name = $element->disorder->term;
                    $disorder_code = $element->code;
                }

                $result .= $eye.' '.$disorder_name." ".$mainCause." ".$disorder_code.", ";
            }
        }

        return substr($result, 0, -2);
    }

    /**
     *
     * @return string
     */
    private function getEcloForPDF()
    {
        if ($this->eclo !== false) {
            switch ($this->eclo) {
                case '1':
                    return '0';
                break;
                case '2':
                    return '1';
                    break;
                default:
                    return '2';
            }
        }

        return '';
    }

    /**
     *
     * @return string
     */
    private function getEcloForVisualyImpaired()
    {
        if ($this->eclo !== false) {
            switch ($this->eclo) {
                case '1':
                    return 'Yes';
                    break;
                case '2':
                    return 'Referred';
                    break;
                default:
                    return 'Not available';
            }
        }

        return '';
    }

    /**
     *
     * @return string
     */
    private function getFieldOfvisionForPDF()
    {
        if ($this->field_of_vision !== false) {
            return ($this->field_of_vision == '1') ? '0' : '1';
        }

        return '';
    }


    public function getPatientTypes()
    {
        $types = [
            Element_OphCoCvi_ClinicalInfo_V1::CVI_TYPE_ADULT => "Diagnosis (for patients 18 years of age or over)",
            Element_OphCoCvi_ClinicalInfo_V1::CVI_TYPE_CHILD => "Diagnosis (for patients under the age of 18)",
        ];

        return $types;
    }

    /**
     * Generate dynamic diagnosis table for Visauly Impaired printout
     * @return string
     */
    private function generateDiagnosisForVisualyImpaired()
    {
        $criteria = new \CDbCriteria;
        $criteria->condition = "active=:active";
        $criteria->addCondition("patient_type=:patient_type");
        $criteria->params = array(
            ':active' => 1,
            ':patient_type' => $this->patient_type
        );

        $disorderSections = OphCoCvi_ClinicalInfo_Disorder_Section::model()->findAll($criteria);

        $table = '';
        if ($disorderSections) {
            foreach ($disorderSections as $section) {
                if ($section->disorders) {
                    $table .= '<tr><td rowspan="'.count($section->disorders).'"><strong>'.$section->name.'</strong></td>';

                    foreach ($section->disorders as $key => $disorder) {
                        if ($key !== 0) {
                            $table .= '<tr>';
                        }

                        $rightEye = '';
                        if ($this->hasCviDisorderForSide($disorder, 'right')) {
                            $rightEye = '<img class="ticked_diagnosis" src="'.realpath(__DIR__ . '/..') . '/assets/img/close_icon.png' .'" />';
                        }

                        $leftEye = '';
                        if ($this->hasCviDisorderForSide($disorder, 'left')) {
                            $leftEye = '<img class="ticked_diagnosis" src="'.realpath(__DIR__ . '/..') . '/assets/img/close_icon.png' .'" />';
                        }

                        if ($this->hasCviDisorderForSide($disorder, 'both')) {
                            $rightEye = '<img class="ticked_diagnosis" src="'.realpath(__DIR__ . '/..') . '/assets/img/close_icon.png' .'" />';
                            $leftEye = '<img class="ticked_diagnosis" src="'.realpath(__DIR__ . '/..') . '/assets/img/close_icon.png' .'" />';
                        }

                        $main_cause = '';
                        if ($this->isCviDisorderMainCauseForSide($disorder, 'right') == 1) {
                            $main_cause = '<br><strong>Main Cause</strong>';
                        }

                        $table .= '
                             <td>'.$disorder->name.$main_cause.'</td>
                             <td>'.$disorder->code.'</td>
                             <td>'.$rightEye.'</td>
                             <td>'.$leftEye.'</td>
                        ';
                        if ($key === 0) {
                            $table .= '</tr>';
                        }
                    }
                    $table .= '</tr>';
                }
            }

            $table .= '
                <tr>
                    <td colspan="2"><strong>Diagnosis not covered in any of the above, specify, including ICD 10 code if known</strong></td>
                    <td colspan="3">'.$this->getNotCoveredDiagnosesForPDF().'</td>
                </tr>
            ';
        }
        return $table;
    }
}
