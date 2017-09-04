<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;


/**
 * Class HistoryRisksEntry
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $element_id
 * @property int $risk_id
 * @property string $other
 * @property string $comments
 *
 * @property Risk $risk
 * @property HistoryRisks $element
 */
class HistoryRisksEntry extends \BaseElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return static
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
        return 'ophciexamination_history_risks_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, risk_id, other, has_risk, comments', 'safe'),
            array('risk_id', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, element_id, risk_id, other, has_risk, comments', 'safe', 'on' => 'search'),
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
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\HistoryRisks', 'element_id'),
            'risk' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExaminationRisk', 'risk_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'risk_id' => 'Risk',
        );
    }

    public function afterValidate()
    {
        if ($this->risk && $this->risk->isOther() && !$this->other) {
            $this->addError('other', 'Other description is required');
        }
        parent::afterValidate();
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
        $criteria->compare('risk_id', $this->allergy_id, true);
        $criteria->compare('other', $this->other, true);
        $criteria->compare('has_risk', $this->has_risk, true);
        $criteria->compare('comments', $this->comments, true);
        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    protected function beforeSave()
    {
        if ($this->isModelDirty()) {
            $this->element->addAudit('edited-risks');
        }
        return parent::beforeSave();
    }

    /**
     * @return string
     */
    public function getDisplayRisk()
    {
        if ($this->other) {
            return $this->other;
        }
        return $this->risk ? $this->risk->name : '';
    }

    /**
     * @return string
     */
    public function getDisplayHasRisk()
    {
        if ($this->has_risk) {
            return 'Present';
        } elseif ($this->has_risk === '0') {
            return 'Not present';
        }
        return 'Not checked';
    }

    /**
     * @param bool $show_status
     * @return string
     */
    public function getDisplay($show_status = false)
    {
        $res = $this->getDisplayRisk();
        if ($show_status) {
            $res .= ' - ' . $this->getDisplayHasRisk();
        }
        if ($this->comments) {
            $res .= ' (' . $this->comments . ')';
        }
        return $res;

    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDisplay(true);
    }

    /**
     * @param $patientId
     *
     * @return array|mixed|null
     */
    public function mostRecentCheckedAlpha($patientId)
    {

        $risk_id = \Yii::app()->db->createCommand()->select('id')->from('ophciexamination_risk')->where('name=:name', array(':name' => 'Alpha blockers'))->queryScalar();
        $criteria = $this->risksByTypeForPatient($risk_id, $patientId);
        $criteria->limit = 1;
        return self::model()->find($criteria);
    }


    protected function risksByTypeForPatient($type_id, $patientId)
    {
        $criteria = new \CDbCriteria();
        $criteria->join = 'join ophciexamination_risk risk on risk.id = t.risk_id ';
        $criteria->join .= 'join et_ophciexamination_history_risks h_risk on t.element_id = h_risk.id ';
        $criteria->join .= 'join event on h_risk.event_id = event.id ';
        $criteria->join .= 'join episode on event.episode_id = episode.id ';
        $criteria->addCondition('risk.id = :type_id');
        $criteria->addCondition('event.deleted <> 1');
        $criteria->addCondition('episode.patient_id = :patient_id');
        $criteria->addCondition('risk.active = 1');
        $criteria->params = array(':patient_id' => $patientId, ':type_id' => $type_id);
        $criteria->order = 'event.created_date DESC';

        return $criteria;
    }

    /**
     * @param $patientId
     *
     * @return array|mixed|null
     */
    public function mostRecentCheckedAnticoag($patientId)
    {
      $risk_id = \Yii::app()->db->createCommand()->select('id')->from('ophciexamination_risk')->where('name=:name', array(':name' => 'Anticoagulants'))->queryScalar();
      $criteria = $this->risksByTypeForPatient($risk_id, $patientId);
      $criteria->limit = 1;
      return self::model()->find($criteria);
    }

}
