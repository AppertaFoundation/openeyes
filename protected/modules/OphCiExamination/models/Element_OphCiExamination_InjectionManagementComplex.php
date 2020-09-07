<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use Yii;

/**
 * This is the model class for table "et_ophciexamination_injectionmanagement".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property bool $left_no_treatment
 * @property bool $right_no_treatment
 * @property int $left_no_treatment_reason_id
 * @property int $right_no_treatment_reason_id
 * @property string $left_no_treatment_reason_other
 * @property string $right_no_treatment_reason_other
 * @property int $left_diagnosis1_id
 * @property int $right_diagnosis1_id
 * @property int $left_diagnosis2_id
 * @property int $right_diagnosis2_id
 * @property int $left_treatment_id
 * @property int $right_treatment_id
 * @property string $right_comments
 * @property string $left_comments
 *
 * The followings are the available model relations:
 * @property OphCiExamination_InjectionManagementComplex_NoTreatmentReason $left_no_treatment_reason
 * @property OphCiExamination_InjectionManagementComplex_NoTreatmentReason $right_no_treatment_reason
 * @property Disorder $left_diagnosis1
 * @property Disorder $right_diagnosis1
 * @property Disorder $left_diagnosis2
 * @property Disorder $right_diagnosis2
 * @property OphCiExamination_InjectionManagementComplex_Answer[] $left_answers
 * @property OphCiExamination_InjectionManagementComplex_Answer[] $right_answers
 * @property OphCiExamination_InjectionManagementComplex_Risk[] $left_risks
 * @property OphCiExamination_InjectionManagementComplex_Risk[] $right_risks
 * @property OphTrIntravitrealinjection_Treatment_Drug $left_treatment - ONLY availabile if OphTrIntravitrealinjection module installed
 * @property OphTrIntravitrealinjection_Treatment_Drug $right_treatment - ONLY availabile if OphTrIntravitrealinjection module installed
 */
class Element_OphCiExamination_InjectionManagementComplex extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    protected $_injection_installed = null;

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
        return 'et_ophciexamination_injectionmanagementcomplex';
    }

    /**
     * returns boolean to indicate if the injection module is installed (and therefore whether we can pick treatments
     * from that module).
     *
     * @return bool
     */
    public function injectionInstalled()
    {
        if ($this->_injection_installed == null) {
            $this->_injection_installed = Yii::app()->hasModule('OphTrIntravitrealinjection');
        }

        return $this->_injection_installed;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('eye_id, left_no_treatment, right_no_treatment, left_no_treatment_reason_id, right_no_treatment_reason_id,
						left_no_treatment_reason_other, right_no_treatment_reason_other, left_diagnosis1_id,
						right_diagnosis1_id, left_diagnosis2_id, right_diagnosis2_id, left_comments, right_comments,
						left_treatment_id, right_treatment_id', 'safe'),
            array('right_treatment_id, left_treatment_id, right_no_treatment_reason_id, left_no_treatment_reason_id', 'default', 'setOnEmpty' => true, 'value' => null),
            array('left_no_treatment', 'requiredIfSide', 'side' => 'left'),
            array('right_no_treatment', 'requiredIfSide', 'side' => 'right'),
            array('left_no_treatment_reason_id', 'requiredIfNoTreatment', 'side' => 'left'),
            array('right_no_treatment_reason_id', 'requiredIfNoTreatment', 'side' => 'right'),
            array('left_no_treatment_reason_other', 'requiredIfNoTreatmentOther', 'side' => 'left'),
            array('right_no_treatment_reason_other', 'requiredIfNoTreatmentOther', 'side' => 'right'),
            array('left_diagnosis1_id', 'requiredIfTreatment', 'side' => 'left'),
            array('right_diagnosis1_id', 'requiredIfTreatment', 'side' => 'right'),
            array('left_diagnosis2_id', 'requiredIfSecondary', 'side' => 'left', 'dependent' => 'left_diagnosis1_id'),
            array('right_diagnosis2_id', 'requiredIfSecondary', 'side' => 'right', 'dependent' => 'right_diagnosis1_id'),
            array('left_answers', 'answerValidation', 'side' => 'left'),
            array('right_answers', 'answerValidation', 'side' => 'right'),
            array('left_treatment_id', 'ifInjectionInstalled', 'side' => 'left', 'check' => 'requiredIfTreatment'),
            array('right_treatment_id', 'ifInjectionInstalled', 'side' => 'right', 'check' => 'requiredIfTreatment'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id, left_no_treatment, right_no_treatment, left_no_treatment_reason_id, right_no_treatment_reason_id,
						left_no_treatment_reason_other, right_no_treatment_reason_other, left_diagnosis_id, right_diagnosis_id,' .
                'left_diagnosis2_id, right_diagnosis2_id, left_comments, right_comments', 'safe', 'on' => 'search',),
        );
    }

    public function sidedFields()
    {
        return array('no_treatment', 'no_treatment_reason_id', 'no_treatment_reason_other', 'diagnosis1_id', 'diagnosis2_id', 'comments');
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
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
                'left_no_treatment_reason' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_InjectionManagementComplex_NoTreatmentReason', 'left_no_treatment_reason_id'),
                'right_no_treatment_reason' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_InjectionManagementComplex_NoTreatmentReason', 'right_no_treatment_reason_id'),
                'left_diagnosis1' => array(self::BELONGS_TO, 'Disorder', 'left_diagnosis1_id'),
                'right_diagnosis1' => array(self::BELONGS_TO, 'Disorder', 'right_diagnosis1_id'),
                'left_diagnosis2' => array(self::BELONGS_TO, 'Disorder', 'left_diagnosis2_id'),
                'right_diagnosis2' => array(self::BELONGS_TO, 'Disorder', 'right_diagnosis2_id'),
                'answers' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_InjectionManagementComplex_Answer', 'element_id'),
                'left_answers' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_InjectionManagementComplex_Answer', 'element_id', 'on' => 'left_answers.eye_id = '.\Eye::LEFT),
                'right_answers' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_InjectionManagementComplex_Answer', 'element_id', 'on' => 'right_answers.eye_id = '.\Eye::RIGHT),
                'risk_assignments' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_InjectionManagementComplex_RiskAssignment', 'element_id'),
                'left_risks' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_InjectionManagementComplex_Risk', 'risk_id', 'through' => 'risk_assignments', 'on' => 'risk_assignments.eye_id = '.\Eye::LEFT),
                'right_risks' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_InjectionManagementComplex_Risk', 'risk_id', 'through' => 'risk_assignments', 'on' => 'risk_assignments.eye_id = '.\Eye::RIGHT),
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
                'left_no_treatment' => 'No treatment',
                'right_no_treatment' => 'No treatment',
                'left_no_treatment_reason_id' => 'Reason for no treatment',
                'right_no_treatment_reason_id' => 'Reason for no treatment',
                'left_no_treatment_reason_other' => 'Please provide other reason for no treatment',
                'right_no_treatment_reason_other' => 'Please provide other reason for no treatment',
                'left_diagnosis1_id' => 'Diagnosis',
                'right_diagnosis1_id' => 'Diagnosis',
                'left_diagnosis2_id' => 'Associated with',
                'right_diagnosis2_id' => 'Associated with',
                'left_risks' => 'Risks',
                'right_risks' => 'Risks',
                'left_comments' => 'Comments',
                'right_comments' => 'Comments',
                'left_treatment_id' => 'Intended Treatment',
                'right_treatment_id' => 'Intended Treatment',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('left_no_treatment', $this->left_no_treatment);
        $criteria->compare('right_no_treatment', $this->right_no_treatment);
        $criteria->compare('left_no_treatment_reason_id', $this->left_no_treatment_reason_id);
        $criteria->compare('right_no_treatment_reason_id', $this->right_no_treatment_reason_id);
        $criteria->compare('left_no_treatment_reason_other', $this->left_no_treatment_reason_other);
        $criteria->compare('right_no_treatment_reason_other', $this->right_no_treatment_reason_other);
        $criteria->compare('eye_id', $this->eye_id);
        $criteria->compare('left_diagnosis1_id', $this->left_diagnosis1_id);
        $criteria->compare('right_diagnosis1_id', $this->right_diagnosis1_id);
        $criteria->compare('left_diagnosis2_id', $this->left_diagnosis2_id);
        $criteria->compare('right_diagnosis2_id', $this->right_diagnosis2_id);
        $criteria->compare('left_comments', $this->left_comments);
        $criteria->compare('right_comments', $this->right_comments);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * extends standard delete method to remove any risk assignments made to it.
     *
     * (non-PHPdoc)
     *
     * @see CActiveRecord::delete()
     */
    public function delete()
    {
        $transaction = Yii::app()->db->getCurrentTransaction() === null
                ? Yii::app()->db->beginTransaction()
                : false;

        try {
            foreach ($this->risk_assignments as $riska) {
                $riska->delete();
            }
            foreach ($this->answers as $answer) {
                $answer->delete();
            }
            if (parent::delete()) {
                if ($transaction) {
                    $transaction->commit();
                }
            } else {
                throw new Exception('unable to delete');
            }
        } catch (Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }
            throw $e;
        }
    }

    /**
     * get the reason for no treatment as a string for given side.
     *
     * @param string $side
     *
     * @return string
     */
    protected function getNoTreatmentReasonName($side)
    {
        if ($ntr = $this->{$side.'_no_treatment_reason'}) {
            if ($ntr->other) {
                return $this->{$side.'_no_treatment_reason_other'};
            } else {
                return $ntr->name;
            }
        }

        return 'Not specified';
    }

    /**
     * get the no treatment reason name for the left side.
     *
     * @return string
     */
    public function getLeftNoTreatmentReasonName()
    {
        return $this->getNoTreatmentReasonName('left');
    }

    /**
     * get the no treatment reason name for the right side.
     *
     * @return string
     */
    public function getRightNoTreatmentReasonName()
    {
        return $this->getNoTreatmentReasonName('right');
    }

    /**
     * validate that all the questions for the set diagnosis have been answered.
     *
     * @param unknown $attribute
     * @param array   $params
     */
    public function answerValidation($attribute, $params)
    {
        $side = $params['side'];

        if (($side == 'left' && !$this->left_no_treatment && $this->eye_id != \Eye::RIGHT) ||
            ($side == 'right' && !$this->right_no_treatment && $this->eye_id != \Eye::LEFT)) {
            $questions = $this->getInjectionQuestionsForSide($side);

            $answer_q_ids = array();
            foreach ($this->{$side.'_answers'} as $ans) {
                $answer_q_ids[] = $ans->question_id;
            }

            foreach ($questions as $required_question) {
                if (!in_array($required_question->id, $answer_q_ids)) {
                    $this->addError($attribute, ucfirst($side).' '.$required_question->question.' must be answered.');
                }
            }
        }
    }

    /**
     * store the answers for the questions asked for the $side diagnosis.
     *
     * @param string $side
     * @param array  $update_answers - associate array of question id to answer value
     */
    public function updateQuestionAnswers($side, $update_answers)
    {
        $current_answers = array();
        $save_answers = array();

        // note we operate on answers relation here, so that we avoid any custom assignment
        // that might have taken place for the purposes of validation (for $side_answers)
        // TODO: when looking at OE-2927 it might be better if we update the interventions in a different way
        // where the changes are stored when set for validation, and then afterSave is used to do the actual database changes
        foreach ($this->answers as $curr) {
            if ($curr->eye_id == $side) {
                $current_answers[$curr->question_id] = $curr;
            }
        }

        // go through each question answer, if there isn't one for this element,
        // create it and store for saving
        // if there is, check if the value is the same ... if it has changed
        // update and store for saving, otherwise remove from the current answers array
        // anything left in current answers at the end is ripe for deleting
        foreach ($update_answers as $question_id => $answer) {
            if (!array_key_exists($question_id, $current_answers)) {
                $s = new OphCiExamination_InjectionManagementComplex_Answer();
                $s->attributes = array('element_id' => $this->id, 'eye_id' => $side, 'question_id' => $question_id, 'answer' => $answer);
                $save_answers[] = $s;
            } else {
                if ($current_answers[$question_id]->answer != $answer) {
                    $current_answers[$question_id]->answer = $answer;
                    $save_answers[] = $current_answers[$question_id];
                }
                // don't want to delete this, so remove from list which we use later to delete
                unset($current_answers[$question_id]);
            }
        }

        // save what needs saving
        foreach ($save_answers as $save) {
            $save->save();
        }
        // delete any that are no longer relevant
        foreach ($current_answers as $curr) {
            $curr->delete();
        }
    }

    /**
     * update the risks for the given side.
     *
     * @param string $side
     * @param int[]  $risk_ids - array of risk ids to assign to the element
     */
    public function updateRisks($side, $risk_ids)
    {
        $current_risks = array();
        $save_risks = array();

        foreach ($this->risk_assignments as $curr) {
            if ($curr->eye_id == $side) {
                $current_risks[$curr->risk_id] = $curr;
            }
        }

        // go through each update risk id, if it isn't assigned for this element,
        // create assignment and store for saving
        // if there is, remove from the current risk array
        // anything left in $current_risks at the end is ripe for deleting
        if (is_array($risk_ids)) {
            foreach ($risk_ids as $risk_id) {
                if (!array_key_exists($risk_id, $current_risks)) {
                    $s = new OphCiExamination_InjectionManagementComplex_RiskAssignment();
                    $s->attributes = array('element_id' => $this->id, 'eye_id' => $side, 'risk_id' => $risk_id);
                    $save_risks[] = $s;
                } else {
                    // don't want to delete later
                    unset($current_risks[$risk_id]);
                }
            }
        // save what needs saving
            foreach ($save_risks as $save) {
                $save->save();
            }
        // delete the rest
            foreach ($current_risks as $curr) {
                $curr->delete();
            }
        }
    }

    /**
     * get the risk options for a given side.
     *
     * @param $side
     *
     * @return array
     */
    public function getRisksForSide($side)
    {
        return OphCiExamination_InjectionManagementComplex_Risk::model()->activeOrPk($this->riskValues)->findAll();
    }

    /**
     * get the risk ids currently in use by the element.
     */
    public function getRiskValues()
    {
        $risk_values = array();

        foreach ($this->risk_assignments as $risk_assignment) {
            $risk_values[] = $risk_assignment->risk_id;
        }

        return $risk_values;
    }

    /**
     * get the list of no treatment reasons that should be used for this element.
     *
     * @return OphCiExamination_InjectionManagementComplex_NoTreatmentReason[]
     */
    public function getNoTreatmentReasons()
    {
        return OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->activeOrPk(
            array($this->left_no_treatment_reason_id, $this->right_no_treatment_reason_id)
        )->findAll();
    }

    /**
     * get the relevant questions for the given side.
     *
     * @param string $side - 'left' or 'right'
     *
     * @return OphCiExamination_InjectionManagementComplex_Question[]
     */
    public function getInjectionQuestionsForSide($side)
    {
        // need to get the questions for the set disorders. And then check if there are already answers on the side
        // if there are, then check for any missing questions, in case they've been disabled since
        $answered_question_ids = array();

        foreach ($this->{$side.'_answers'} as $answer) {
            $answered_question_ids[] = $answer->question_id;
        }

        $questions = array();
        $qids = array();
        if ($did = $this->{$side.'_diagnosis1_id'}) {
            foreach ($this->getInjectionQuestionsForDisorderId($did, $answered_question_ids) as $question) {
                $questions[] = $question;
                $qids[] = $question->id;
            }
        }
        if ($did = $this->{$side.'_diagnosis2_id'}) {
            foreach ($this->getInjectionQuestionsForDisorderId($did, $answered_question_ids) as $question) {
                $questions[] = $question;
                $qids[] = $question->id;
            }
        }

        foreach ($this->{$side.'_answers'} as $answer) {
            if (!in_array($answer->question_id, $qids)) {
                $questions[] = $answer->question;
            }
        }

        return $questions;
    }

    /**
     * return the questions for a given disorder id.
     *
     * @param int   $disorder_id
     * @param array $answered_question_ids
     *
     * @throws Exception
     */
    public function getInjectionQuestionsForDisorderId($disorder_id, $answered_question_ids = null)
    {
        if (!$disorder_id) {
            throw new Exception('Disorder id required for injection questions');
        }

        $criteria = new \CDbCriteria();
        $criteria->condition = 'disorder_id = :disorder_id';
        $criteria->params = array(':disorder_id' => $disorder_id);
        $criteria->order = 'display_order asc';

        // get the questions
        return OphCiExamination_InjectionManagementComplex_Question::model()->activeOrPk($answered_question_ids)->findAll($criteria);
    }

    /**
     * get the answer that has been set for the $side and $question_id.
     *
     * @param unknown $side
     * @param unknown $question_id
     */
    public function getQuestionAnswer($side, $question_id)
    {
        foreach ($this->{$side.'_answers'} as $answer) {
            if ($answer->question_id == $question_id) {
                return $answer->answer;
            }
        }
    }

    public function getAllDisorders()
    {
        $disorders = array();
        foreach ($this->getLevel1Disorders() as $disorder) {
            $disorders[] = $disorder;
            foreach ($this->getLevel2Disorders($disorder) as $l2_disorder) {
                $disorders[] = $l2_disorder;
            }
        }

        return $disorders;
    }

    /**
     * Get a list of level 1 disorders for this element (appends any level 1 disorder that has been selected for this
     * element but aren't part of the default list).
     *
     * @return Disorder[]
     */
    public function getLevel1Disorders()
    {
        $disorders = array();
        $disorder_ids = array();
        if ($api = Yii::app()->moduleAPI->get('OphCoTherapyapplication')) {
            $therapy_disorders = $api->getLevel1Disorders();

            foreach ($therapy_disorders as $td) {
                $disorders[] = $td;
                $disorder_ids[] = $td->id;
            }
        }

        // if this element has been created with a disorder outside of the standard list, needs to be available in the
        // list for selection to be maintained
        foreach (array('left', 'right') as $side) {
            if ($this->{$side.'_diagnosis1_id'} && !in_array($this->{$side.'_diagnosis1_id'}, $disorder_ids)) {
                $disorders[] = $this->{$side.'_diagnosis1'};
            }
        }

        return $disorders;
    }

    /**
     * retrieve a list of disorders that are defined as level 2 disorders for the given disorder.
     *
     * @param unknown $therapyDisorder
     *
     * @return Disorder[]
     */
    public function getLevel2Disorders($disorder)
    {
        $disorders = [];
        $disorder_ids = [];

        if ($api = Yii::app()->moduleAPI->get('OphCoTherapyapplication')) {
            $disorders = $api->getLevel2Disorders($disorder->id);
            foreach ($disorders as $d) {
                $disorder_ids[] = $d->id;
            }

            foreach (array('left', 'right') as $side) {
                if ($this->{$side.'_diagnosis1_id'} == $disorder->id
                    && $this->{$side.'_diagnosis2_id'}
                    && !in_array($this->{$side.'_diagnosis2_id'}, $disorder_ids)) {
                    $disorders[] = $this->{$side.'_diagnosis2'};
                }
            }
        }

        return $disorders;
    }

    /**
     * simple wrapper around requiredIfSide that checks the no treatment status flag before checking the side required
     * attribute.
     *
     * @param string $attribute
     * @param array  $params
     */
    public function requiredIfTreatment($attribute, $params)
    {
        $side = $params['side'];
        if (($side == 'left' && $this->eye_id != \Eye::RIGHT) || ($side == 'right' && $this->eye_id != \Eye::LEFT)) {
            if (!$this->{$side.'_no_treatment'} && $this->$attribute == null) {
                $this->addError($attribute, ucfirst($side).' '.$this->getAttributeLabel($attribute).' must be provided when treatment required');
            }
        }
    }

    /**
     * checks value defined when no treatment is set on the element.
     *
     * @param $attribute
     * @param $params
     */
    public function requiredIfNoTreatment($attribute, $params)
    {
        $side = $params['side'];
        if (($side == 'left' && $this->eye_id != \Eye::RIGHT) || ($side == 'right' && $this->eye_id != \Eye::LEFT)) {
            if ($this->{$side.'_no_treatment'} && $this->$attribute == null) {
                $this->addError($attribute, ucfirst($side).' '.$this->getAttributeLabel($attribute).' must be provided when there is no treatment');
            }
        }
    }

    /**
     * checks $attribute defined when the no treatment reason is an 'other' type.
     *
     * @param $attribute
     * @param $params
     */
    public function requiredIfNoTreatmentOther($attribute, $params)
    {
        $side = $params['side'];
        if ($this->{$side.'_no_treatment_reason'} && $this->{$side.'_no_treatment_reason'}->other && (is_null($this->$attribute) || strlen(trim($this->$attribute)) == 0)) {
            $this->addError($attribute, ucfirst($side).' '.$this->getAttributeLabel($attribute));
        }
    }

    /**
     * check a level 2 diagnosis is provided for level 1 diagnoses that require it (need to check the side as well though).
     */
    public function requiredIfSecondary($attribute, $params)
    {
        if (($params['side'] == 'left' && $this->eye_id != \Eye::RIGHT) || ($params['side'] == 'right' && $this->eye_id != \Eye::LEFT)) {
            if ($this->{$params['dependent']} && (!$this->$attribute || empty($this->$attribute))) {
                if ($api = Yii::app()->moduleAPI->get('OphCoTherapyapplication')) {
                    if (count($api->getLevel2Disorders($this->{$params['dependent']}))) {
                        $disorder = \Disorder::model()->findByPk($this->{$params['dependent']});
                        $this->addError($attribute, $disorder->term.' must be associated with another diagnosis');
                    }
                }
            }
        }
    }

    /**
     * pass through validation function that will run the 'check' attribute method if the injection module is installed.
     *
     * @param $attribute
     * @param $params
     */
    public function ifInjectionInstalled($attribute, $params)
    {
        if ($this->injectionInstalled()) {
            $check = $params['check'];
            $this->$check($attribute, $params);
        }
    }
    /**
     * return the side (Eye::BOTH, \Eye::LEFT or \Eye::RIGHT) that should be injected if one should be. null otherwise.
     *
     * @return int|null
     */
    public function getInjectionSide()
    {
        $left = false;
        $right = false;
        if ($this->hasLeft()) {
            if (!$this->left_no_treatment) {
                $left = true;
            }
        }
        if ($this->hasRight()) {
            if (!$this->right_no_treatment) {
                $right = true;
            }
        }
        if ($left) {
            if ($right) {
                return \Eye::BOTH;
            } else {
                return \Eye::LEFT;
            }
        } elseif ($right) {
            return \Eye::RIGHT;
        }

        return;
    }

    /**
     * get the treatments list to select from for this element on the given side.
     *
     * @param $side
     *
     * @return OphTrIntravitrealinjection_Treatment_Drug[]|null
     */
    public function getInjectionTreatments($side)
    {
        if ($this->injectionInstalled()) {
            # Note that this will namespaced to modules at some point.
            $treatments = \OphTrIntravitrealinjection_Treatment_Drug::model()->active()->findAll();
            if ($current_id = $this->{$side.'_treatment_id'}) {
                $treatment_list = array();

                foreach ($treatments as $treatment) {
                    if ($treatment->id == $current_id) {
                        return $treatments;
                    }
                    $treatment_list[] = $treatment;
                }
                $treatment_list[] = $this->{$side.'_treatment'};
                $treatments = $treatment_list;
            }

            return $treatments;
        }
    }

    /**
     * return the treatment drug for the left side if defined.
     *
     * defines relation to external model, hence not using the yii magic relations definition
     *
     * @return OphTrIntravitrealinjection_Treatment_Drug|null
     */
    public function getleft_treatment()
    {
        if ($this->injectionInstalled()) {
            if ($this->hasLeft() && $this->left_treatment_id) {
                return \OphTrIntravitrealinjection_Treatment_Drug::model()->findByPk($this->left_treatment_id);
            }
        }
    }

    /**
     * return the treatment drug for the right side if defined.
     *
     * defines relation to external model, hence not using the yii magic relations definition
     *
     * @return OphTrIntravitrealinjection_Treatment_Drug|null
     */
    public function getright_treatment()
    {
        if ($this->injectionInstalled()) {
            if ($this->hasRight() && $this->right_treatment_id) {
                return \OphTrIntravitrealinjection_Treatment_Drug::model()->findByPk($this->right_treatment_id);
            }
        }
    }

    /**
     * get the diagnosis string for the give side.
     *
     * @param string $side - left or right
     *
     * @return string string
     */
    protected function getDiagnosisString($side)
    {
        $res = '';
        if ($this->{$side.'_diagnosis1_id'}) {
            $res = $this->{$side.'_diagnosis1'}->term;
        }
        if ($this->{$side.'_diagnosis2_id'}) {
            $res .= ' associated with '.$this->{$side.'_diagnosis2'}->term;
        }

        return $res;
    }

    /**
     * get the diagnosis string for the right.
     *
     * @return string
     */
    public function getRightDiagnosisString()
    {
        if ($this->hasRight()) {
            return $this->getDiagnosisString('right');
        }
    }

    /**
     * get the diagnosis string for the left.
     *
     * @return string
     */
    public function getLeftDiagnosisString()
    {
        if ($this->hasLeft()) {
            return $this->getDiagnosisString('left');
        }
    }

    /**
     * gets a string of the information contained in this element for the given side.
     *
     * @param $side
     *
     * @return string
     */
    protected function getLetterStringForSide($side)
    {
        $res = ucfirst($side)." Eye:\n";
        if ($notreatment = $this->{$side.'_no_treatment_reason'}) {
            $res .= $notreatment->getLetter_string();
            if ($notreatment->other) {
                $other = trim($this->{$side.'_no_treatment_reason_other'});
                $res .= ' '.$other;
                if (substr_compare($other, '.', strlen($other) - 1) !== 0) {
                    $res .= '.';
                }
            }
            $res .= "\n";
        } else {
            if ($treat = $this->{$side.'_treatment'}) {
                $res .= 'Treatment: '.$treat->name."\n";
            }
            $res .= 'Diagnosis: '.$this->{'get'.ucfirst($side).'DiagnosisString'}()."\n";
            if ($risks = $this->{$side.'_risks'}) {
                $res .= 'Risks: ';
                foreach ($risks as $i => $risk) {
                    if ($i > 0) {
                        $res .= ', ';
                    }
                    $res .= $risk->name;
                }
                $res .= "\n";
            }
            if ($comments = $this->{$side.'_comments'}) {
                $res .= 'Comments: '.$comments."\n";
            }
        }

        return $res;
    }

    /**
     * get the string of this element for use in correspondence.
     *
     * @return string
     */
    public function getLetter_string()
    {
        $res = "Injection Management:\n";
        if ($this->hasRight()) {
            $res .= $this->getLetterStringForSide('right');
        }
        if ($this->hasLeft()) {
            $res .= $this->getLetterStringForSide('left');
        }

        return $res;
    }

    public function canCopy()
    {
        return true;
    }

    protected function getSummaryStringForSide($side)
    {
        if ($this->{$side . '_no_treatment_reason'}) {
            return $this->{$side . '_no_treatment_reason'}->other ?
                $this->{$side . '_no_treatment_reason_other'} :
                $this->{$side . '_no_treatment_reason'}->name;
        } else {
            return $this->{$side . '_treatment'};
        }
    }

    public function __toString() {
        $res = array();
        if ($this->hasRight()) {
            $res[] = 'R: ' . $this->getSummaryStringForSide('right');
        }
        if ($this->hasLeft()) {
            $res[] = 'L: ' . $this->getSummaryStringForSide('left');
        }
        return implode(', ', $res);
    }
}
