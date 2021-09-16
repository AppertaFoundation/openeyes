<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoCvi\models;

/**
 * This is the model class for table "et_ophcocvi_clericinfo".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $event_id
 * @property integer $employment_status_id
 * @property integer $preferred_info_fmt_id
 * @property string $info_email
 * @property integer $contact_urgency_id
 * @property integer $preferred_language_id
 * @property integer $preferred_comm_id
 * @property string $social_service_comments
 * @property string $preferred_comm_text
 * @property string $preferred_comm_other
 *
 * The followings are the available model relations:
 *
 * @property \ElementType $element_type
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property OphCoCvi_ClericalInfo_EmploymentStatus $employment_status
 * @property OphCoCvi_ClericalInfo_PreferredInfoFmt $preferred_info_fmt
 * @property OphCoCvi_ClericalInfo_PatientFactor $factor
 * @property OphCoCvi_ClericalInfo_ContactUrgency $contact_urgency
 * @property OphCoCvi_ClericalInfo_PatientFactor_Answer[] $patient_factor_answers
 * @property \Language $preferred_language
 */

class Element_OphCoCvi_ClericalInfo_V1 extends \BaseEventTypeElement
{

    public $preferred_format_ids = array();
    /**
     * Returns the static model of the specified AR class.
     * @param null|string $className
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
        return 'et_ophcocvi_clericinfo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array(
                'preferred_format_ids, event_id, preferred_info_fmt_id, contact_urgency_id, preferred_language_id, social_service_comments, preferred_language_text, preferred_comm_id, preferred_comm, preferred_comm_other, preferred_format_other, interpreter_required',
                'safe'
            ),
            array(
                'preferred_info_fmt_id',
                'required',
                'on' => 'finalise'
            ),
            array(
                'info_email', 'length', 'max' => 255
            ),
            array(
                'id, event_id, preferred_info_fmt_id, info_email, contact_urgency_id, preferred_language_id, social_service_comments, ',
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
            'event' => array(self::BELONGS_TO, '\Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'preferred_info_fmt' => array(
                self::BELONGS_TO,
                'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt',
                'preferred_info_fmt_id',
            ),
            'preferred_format' => array (
                self::BELONGS_TO,
                'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredFormat',
                'preferred_format_id',
            ),
            'preferred_language' => array(self::BELONGS_TO, 'Language', 'preferred_language_id'),
            'patient_factor_answers' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PatientFactor_Answer',
                'element_id'
            ),
            'preferred_format_assignments' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\OphCoCvi_Clericalinfo_Preferredformat_Assignment',
                'element_id'
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
            'event_id' => 'Event',
            'employment_status_id' => 'Employment status',
            'preferred_info_fmt_id' => 'Preferred method of contact',
            'preferred_comm' => 'Preferred method of communication e.g. BSL, deafblind manual?',
            'preferred_format_id' => 'Preferred format of information',
            'preferred_format_other' => 'Other information format (specify)',
            'contact_urgency_id' => 'Contact urgency',
            'preferred_language_id' => 'Preferred language',
            'preferred_language_text' => "Other language (specify)",
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
        $criteria->compare('preferred_info_fmt_id', $this->preferred_info_fmt_id);
        $criteria->compare('contact_urgency_id', $this->contact_urgency_id);
        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function afterValidate()
    {
        if ($this->getScenario() == 'finalise') {
            if (empty($this->preferred_format_assignments) && empty($this->preferred_format_other)) {
                $this->addError('preferred_format_ids', "Preferred format of information cannot be blank.");
            }
        }

        parent::afterValidate();
    }

    public function beforeDelete()
    {
        OphCoCvi_ClericalInfo_PatientFactor_Answer::model()->deleteAllByAttributes(array("element_id" => $this->getPrimaryKey()));
        parent::beforeDelete();
    }

    /**
     * Retrieves a list of patient factor
     *
     * @param $element_id
     * @return array
     */
    public function patientFactorList($element_id)
    {
        $factors = array();
        $element = $this::model()->findByPk($element_id);
        $patient_factor = OphCoCvi_ClericalInfo_PatientFactor::model()->findAll('`active` = 1');
        foreach ($patient_factor as $key => $factor) {
            $factors[$key]['id'] = $factor->id;
            $factors[$key]['name'] = $factor->name;
            $factors[$key]['is_comments'] = $factor->require_comments;
            $factors[$key]['label'] = $factor->comments_label;
            $factors[$key]['is_factor'] = OphCoCvi_ClericalInfo_PatientFactor_Answer::model()->getFactorAnswer($factor->id,
                $element_id);
            $factors[$key]['comments'] = OphCoCvi_ClericalInfo_PatientFactor_Answer::model()->getComments($factor->id,
                $element_id);
        }
        return $factors;
    }

    /**
     * @param $answer
     * @param $data
     * @throws \Exception
     */
    private function updateAnswer($answer, $data)
    {
        $answer->element_id = $this->id;
        $answer->is_factor = isset($data['is_factor']) ? $data['is_factor'] : null;
        $answer->comments = isset($data['comments']) ? $data['comments'] : null;
        if (!$answer->save()) {
            throw new \Exception('Unable to save CVI Disorder Section Comment: ' . print_r($answer->getErrors(), true));
        }
    }

    /**
     * @param $data
     * @throws \CDbException
     * @throws \Exception
     */
    public function updatePatientFactorAnswers($data)
    {
        $current = $this->getRelated('patient_factor_answers', true);

        while ($answer = array_shift($current)) {
            if (array_key_exists($answer->patient_factor_id, $data)) {
                $this->updateAnswer($answer, $data[$answer->patient_factor_id]);
                unset($data[$answer->patient_factor_id]);
            } else {
                if (!$answer->delete()) {
                    throw new \Exception('Unable to delete CVI Patient Factor Answer: ' . print_r($answer->getErrors(), true));
                }
            }
        }

        foreach ($data as $factor_id => $values) {
            $answer = new OphCoCvi_ClericalInfo_PatientFactor_Answer();
            $answer->patient_factor_id = $factor_id;
            $this->updateAnswer($answer, $values);
            if (!$answer->save()) {
                throw new \Exception('Unable to save CVI Patient Factor Answer: ' . print_r($answer->getErrors(), true));
            }
        }
    }

    public function updatePreferredFormats($data)
    {
        OphCoCvi_Clericalinfo_Preferredformat_Assignment::model()->deleteAllByAttributes(['element_id' => $this->id]);
        if (!empty($data)) {
            foreach ($data as $format_id => $values) {
                $preferred_format = new OphCoCvi_Clericalinfo_Preferredformat_Assignment();
                $preferred_format->preferred_format_id = $values;
                $preferred_format->element_id = $this->id;
                if (!$preferred_format->save()) {
                    throw new \Exception('Unable to save CVI Preferred format: ' . print_r($preferred_format->getErrors(), true));
                }
            }
        }
    }

    /**
     * @param OphCoCvi_ClericalInfo_PatientFactor $factor
     * @return OphCoCvi_ClericalInfo_PatientFactor_Answer|null
     */
    public function getPatientFactorAnswer(OphCoCvi_ClericalInfo_PatientFactor $factor)
    {
        foreach ($this->patient_factor_answers as $answer) {
            if ($answer->patient_factor_id == $factor->id) {
                return $answer;
            }
        }
        return null;
    }

    /*
     * Get elements for CVI PDF
     *
     * @return array
     */
    public function getElementsForCVIpdf()
    {
        $preferredFormat = $this->getPreferredFormatForPDF();

        $elements = [
            'If you are an adult do you live alone?'                => $this->getPatientFactorAnswersForPDF( '1v1' ),
            'Does someone support you with your care?'              => $this->getPatientFactorAnswersForPDF( '2v1' ),
            'Do you have difficulties with your physical mobility?' => $this->getPatientFactorAnswersForPDF( '3v1' ),
            'Do you have difficulties with your hearing?'           => $this->getPatientFactorAnswersForPDF( '4v1' ),
            'Do you have a learning disability?'                    => $this->getPatientFactorAnswersForPDF( '5v1' ),
            'Do you have a diagnosis of dementia?'                  => $this->getPatientFactorAnswersForPDF( '6v1' ),
            'Are you employed?'                                     => $this->getPatientFactorAnswersForPDF( '7v1' ),
            'Are you in full-time education?'                       => $this->getPatientFactorAnswersForPDF( '8v1' ),
            'If the patient is a baby, child or young person, is your child/are you known to the specialist visua' => $this->getPatientFactorAnswersForPDF( '9v1' ),
            'further_relevant_info' => $this->getPatientFurterInfoForPDF(),
            'Preferred method of contact?' => $this->getPreferredContactForPDF(),
            'Preferred_method_of_contact_string' => $this->preferred_info_fmt->name,
            'Pref_method'   => $this->preferred_comm,
            'Preferred method of communication: Large print 18' => $preferredFormat[1],
            'Preferred method of communication: Large print 22' => $preferredFormat[2],
            'Preferred method of communication: Large print 26' => $preferredFormat[3],
            'Preferred method of communication: Easy-Read' => $preferredFormat[4],
            'Preferred method of communication: Audio CD' => $preferredFormat[5],
            'Preferred method of communication: Email' => $preferredFormat[6],
            'Preferred method of communication: Other (specify)' => $preferredFormat[7],
            'Other specify' => $this->preferred_format_other,
            'Preferred method of communication: I don’t know and need an assessment' => $preferredFormat[7],
            'Pref_Language' => $this->getPreferredLanguage(),
            'Preferred_format_of_information_visualy_impaired' => $this->getPreferredFormatOfInformation()
        ];

        return $elements;
    }

    /**
     * Set prefferect contact id for PDF
     * @return string|int
     */
    private function getPreferredContactForPDF()
    {
        switch ($this->preferred_info_fmt->id) {
            case 6:
                return 0;
            break;
            case 7:
                return 1;
            break;
            case 8:
                return 2;
            break;
        }
        return '';
    }

    /**
     *
     * @return string
     */
    private function getPreferredFormatForPDF()
    {
        $result = [
            1 => 'Off', //Large print 18
            2 => 'Off', //Large print 22
            3 => 'Off', //Large print 26
            4 => 'Off', //Easy read
            5 => 'Off', //Audio CD
            6 => 'Off', //Email
            7 => 'Off', //Other
        ];

        /* Tick other field in pdf */
        if (!empty($this->preferred_format_assignments)) {
            foreach ($this->preferred_format_assignments as $format) {
                $result[$format->preferred_format_id] = "Yes";
            }
        }

        if (strlen($this->preferred_format_other) > 0) {
            $result[7] = "Yes";
        }

        return $result;
    }

    private function getPreferredFormatOfInformation()
    {
        $result = '';

        if ($this->preferred_format_other) {
            $result .= $this->preferred_format_other;
        }
        if (!empty($this->preferred_format_assignments)) {
            $result .= $this->preferred_format_other ? ', ' : '';
            foreach ($this->preferred_format_assignments as $format) {
                $result .= $format->preferred_format->name."<br>";
            }
        }

        return $result;
    }

    /**
     * Get patient preferred language
     * @return string
     */
    private function getPreferredLanguage()
    {
        $interpreter = ', No interpreter required';
        if ($this->interpreter_required == 1) {
            $interpreter = ', Interpreter required';
        }

        $lang = $this->preferred_language ? $this->preferred_language->name : '';
        if ($this->preferred_language_text) {
            $lang .= $this->preferred_language ? ', ' : '';
            $lang .= $this->preferred_language_text;
        }
        if (!$this->preferred_language_text && !$this->preferred_language) {
            $lang .= 'None';
            $interpreter = "";
        }

        return $lang.$interpreter;
    }

    /**
     * Get patient answers by code
     * @param type $code
     * @return string
     */
    private function getPatientFactorAnswersForPDF($code)
    {
        $result = "";
        if ($this->patient_factor_answers) {
            foreach ($this->patient_factor_answers as $answer) {
                if ($answer->patient_factor->code == $code) {
                    switch ($answer->is_factor) {
                        case 0:
                            return "1";
                        break;
                        case 1:
                            return "0";
                        break;
                        case 2:
                            return "2";
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get patient furter information
     * @return type
     */
    private function getPatientFurterInfoForPDF()
    {
        $result = '';
        if ($this->patient_factor_answers) {
            foreach ($this->patient_factor_answers as $answer) {
                if (($answer->patient_factor->require_comments == 1) && (!empty($answer->comments))) {
                    if (!empty($result)) {
                        $result .= ', ';
                    }
                    $result .= $answer->comments;
                }
            }
        }

        return $result;
    }
}
