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
 * @property string $social_service_comments
 *
 * The followings are the available model relations:
 *
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property OphCoCvi_ClericalInfo_EmploymentStatus $employment_status
 * @property OphCoCvi_ClericalInfo_PreferredInfoFmt $preferred_info_fmt
 * @property OphCoCvi_ClericalInfo_PatientFactor $factor
 * @property OphCoCvi_ClericalInfo_ContactUrgency $contact_urgency
 * @property OphCoCvi_ClericalInfo_PatientFactor_Answer[] $patient_factor_answers
 * @property Language $preferred_language
 */

class Element_OphCoCvi_ClericalInfo extends \BaseEventTypeElement
{
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
     * Pass English as default Preferred Language id
     */
    public function init()
    {
        $preferred_language_id= \Language::model()->findByAttributes(array('name'=>'English'));
        $this->preferred_language_id = $this->preferred_language_id ? $this->preferred_language_id : $preferred_language_id;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array(
                'event_id, employment_status_id, preferred_info_fmt_id, info_email, contact_urgency_id, preferred_language_id, social_service_comments, preferred_language_text, ',
                'safe'
            ),
            array(
                'employment_status_id, preferred_info_fmt_id, contact_urgency_id, preferred_language_id',
                'required',
                'on' => 'finalise'
            ),
            array(
                'info_email', 'length', 'max' => 255
            ),
            array(
                'id, event_id, employment_status_id, preferred_info_fmt_id, info_email, contact_urgency_id, preferred_language_id, social_service_comments, ',
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
            'employment_status' => array(
                self::BELONGS_TO,
                'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_EmploymentStatus',
                'employment_status_id'
            ),
            'preferred_info_fmt' => array(
                self::BELONGS_TO,
                'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt',
                'preferred_info_fmt_id'
            ),
            'contact_urgency' => array(
                self::BELONGS_TO,
                'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_ContactUrgency',
                'contact_urgency_id'
            ),
            'preferred_language' => array(self::BELONGS_TO, 'Language', 'preferred_language_id'),
            'patient_factor_answers' => array(
                self::HAS_MANY,
                'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PatientFactor_Answer',
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
            'preferred_info_fmt_id' => 'Preferred information format',
            'info_email' => 'Info email',
            'contact_urgency_id' => 'Contact urgency',
            'preferred_language_id' => 'Preferred language',
            'social_service_comments' => 'Social service comments',
            'preferred_language_text' => "Other Language",
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
        $criteria->compare('employment_status_id', $this->employment_status_id);
        $criteria->compare('preferred_info_fmt_id', $this->preferred_info_fmt_id);
        $criteria->compare('info_email', $this->info_email);
        $criteria->compare('contact_urgency_id', $this->contact_urgency_id);
        $criteria->compare('preferred_language_id', $this->preferred_language_id);
        $criteria->compare('social_service_comments', $this->social_service_comments);
        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * To generate the employment status array for the pdf
     *
     * @return array
     */
    public function generateStructuredEmploymentStatus()
    {
        $data = array();
        $employment_statuses = OphCoCvi_ClericalInfo_EmploymentStatus::model()->findAll('`active` = ?', array(1), array('order' => 'display_order asc'));
        if (count($employment_statuses)) {
            $data[] = 'Is the patient:';
            foreach ($employment_statuses as $employment) {
                $data[] = $employment->name;
                $data[] = ($this->employment_status_id === $employment->id) ? 'X' : '';
            }
        }
        return array($data);
    }

    /**
     * To generate the contact urgency array for the pdf
     *
     * @return array
     */
    public function generateStructuredContactUrgency()
    {
        $data = array();
        $contactUrgencies = OphCoCvi_ClericalInfo_ContactUrgency::model()->findAll(array('order' => 'display_order asc'));
        foreach ($contactUrgencies as $contactUrgency) {
            $key = $contactUrgency->name;
            $data[] = array(($this->contact_urgency_id === $contactUrgency->id) ? 'X' : '', $key);
        }
        return $data;
    }

    /**
     * @return array
     */
    protected function generateStructuredPatientFactors()
    {
        $data = array();

        foreach (OphCoCvi_ClericalInfo_PatientFactor::model()->active()->findAll() as $factor) {
            $answer = $this->getPatientFactorAnswer($factor);
            if (!$answer || $answer->is_factor == 2) {
                $isFactor = '';
            } elseif ($answer->is_factor == 1) {
                $isFactor = 'Y';
            } elseif ($answer->is_factor == 0) {
                $isFactor = 'N';
            }

            $comments = $answer ? $answer->comments : '';
            $label = $factor->name;
            if ($factor->require_comments) {
                $label .= "\n{$factor->comments_label}: {$comments}";
            }
            $data[] = array($label, $isFactor);
        }

        return $data;
    }

    /**
     * Returns an associative array of the data values for printing
     */
    public function getStructuredDataForPrint()
    {
        $result = array();
        $result['patientFactors'] = $this->generateStructuredPatientFactors();
        $result['employmentStatus'] = $this->generateStructuredEmploymentStatus();
        $result['contactUrgency'] = $this->generateStructuredContactUrgency();

        if ($fmt = $this->preferred_info_fmt) {
            $result['preferredInfoFormat' . $fmt->code] = 'X';
            if ($fmt->require_email) {
                $result['preferredInfoFormatEmailAddress'] = $this->info_email ?: ' ';
            }
            else {
                $result['preferredInfoFormatEmailAddress'] = ' ';
            }
        }

        if ($this->preferred_language_text){
            $result['preferredLanguage'] = $this->preferred_language_text;
        } else {
            $result['preferredLanguage'] = $this->preferred_language ? $this->preferred_language->name : ' ';
        }
        $result['socialServiceComments'] = $this->social_service_comments;

        return $result;
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

        $patient_factor = OphCoCvi_ClericalInfo_PatientFactor::model()->findAll('`active` = ? and event_type_version = ?', array(1, $element->event->version));
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
            }
            else {
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
}
