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

namespace OEModule\OphCiExamination\models;

use services\DateTime;

/**
 * This is the model class for table "et_ophciexamination_historyrisk".
 *
 * The followings are the available columns in table:
 *
 * @property int                                $id
 * @property int                                $event_id
 * @deprecated since v2.0.0 (see HistoryRisks as the replacement Element)
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_HistoryRisk extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_GlaucomaRisk the static model class
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
        return 'et_ophciexamination_examinationrisk';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, event_id, anticoagulant, alphablocker, anticoagulant_name, alpha_blocker_name', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, anticoagulant, alphablocker', 'safe', 'on' => 'search'),
            array('anticoagulant_name', 'validateName', 'type' => 'anticoagulant'),
            array('alpha_blocker_name', 'validateName', 'type' => 'alphablocker'),

        );
    }

    /**
     * Validate the drug name
     *
     * @param $attribute
     * @param $params
     */
    public function validateName($attribute, $params)
    {
        if ($this->$params['type'] === '1' && !$this->$attribute) {
            $this->addError($attribute, 'When checked a drug name is required');
        }

        if ($this->$params['type'] !== '1' && $this->$attribute) {
            $this->addError($attribute, 'A drug name cannot be supplied without selecting yes.');
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
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
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
            'anticoagulant' => 'Patient is on anticoagulants? ',
            'alphablocker' => 'Patient is taking alpha-blockers? ',
            'anticoagulant_name' => 'Anticoagulant Name',
            'alpha_blocker_name' => 'Alpha-blocker Name',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('anticoagulant', $this->anticoagulant, true);
        $criteria->compare('alphablocker', $this->anticoagulant, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the view text for anticoagulant.
     *
     * @return string
     */
    public function anticoagulantText()
    {
        return 'Anticoagulants: ' . $this->yesNoText($this->anticoagulant) . (($this->anticoagulant_name) ? ' - ' . $this->anticoagulant_name : '');
    }

    /**
     * @return string
     */
    public function alphaBlockerText()
    {
        return 'Alpha-Blockers: ' . $this->yesNoText($this->alphablocker) . (($this->alpha_blocker_name) ? ' - ' . $this->alpha_blocker_name : '');
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function yesNoText($value)
    {
        $text = '';
        switch ($value) {
            case '0':
                $text = 'Not Checked';
                break;
            case '1':
                $text = 'Yes';
                break;
            case '2':
                $text = 'No';
                break;
        }

        return $text;
    }

    /**
     * @inheritdoc
     */
    public function canCopy()
    {
        return true;
    }


    /**
     * @param $patientId
     *
     * @return array|mixed|null
     */
    public function mostRecentCheckedAlpha($patientId)
    {
        return $this->mostRecentChecked('alphablocker', $patientId);
    }

    /**
     * @param $patientId
     *
     * @return array|mixed|null
     */
    public function mostRecentCheckedAnticoag($patientId)
    {
        return $this->mostRecentChecked('anticoagulant', $patientId);
    }

    /**
     * @param $patientId
     * @param $date
     *
     * @return array|mixed|null
     */
    public function previousCheckedAlpha($patientId, $date = 'now')
    {
        return $this->previousChecked('alphablocker', $patientId, $date);
    }

    /**
     * @param $patientId
     * @param $date
     *
     * @return array|mixed|null
     */
    public function previousCheckedAnticoag($patientId, $date = 'now')
    {
        return $this->previousChecked('anticoagulant', $patientId, $date);
    }

    /**
     * Find the most recent element that has actually been checked
     *
     * Finds the most recent element where the question of $type has
     * actually been checked, yes or no.
     *
     * @param $type
     * @param $patientId
     *
     * @return array|mixed|null
     */
    protected function mostRecentChecked($type, $patientId)
    {
        $criteria = $this->risksByTypeForPatient($type, $patientId);
        $criteria->limit = 1;

        return self::model()->find($criteria);
    }

    /**
     * @param        $type
     * @param        $patientId
     * @param string $before
     *
     * @return array|mixed|null
     */
    protected function previousChecked($type, $patientId, $before = 'now')
    {
        $date = new \DateTime($before);
        $criteria = $this->risksByTypeForPatient($type, $patientId);
        $criteria->limit = 1;
        $criteria->addCondition('event.event_date < :date');
        $criteria->params['date'] = $date->format('Y-m-d H:i:s');

        return self::model()->find($criteria);
    }

    /**
     * @param $type
     * @param $patientId
     * @return \CDbCriteria
     */
    protected function risksByTypeForPatient($type, $patientId)
    {
        $criteria = new \CDbCriteria();
        $criteria->join = 'join event on t.event_id = event.id ';
        $criteria->join .= 'join episode on event.episode_id = episode.id ';
        $criteria->addCondition($type . ' > 0');
        $criteria->addCondition('event.deleted <> 1');
        $criteria->addCondition('episode.patient_id = :patient_id');
        $criteria->params = array('patient_id' => $patientId);
        $criteria->order = 'event.event_date DESC';

        return $criteria;
    }

}
