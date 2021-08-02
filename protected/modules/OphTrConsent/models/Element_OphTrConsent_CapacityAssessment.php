<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "et_ophtrconsent_capacity_assessment".
 *
 * The followings are the available columns in table:
 *
 * @property string $how_judgement_was_made
 * @property string $evidence
 * @property string $attempts_to_assist
 * @property string $basis_of_decision
 *
 * @property OphTrConsent_LackOfCapacityReason[] $lackOfCapacityReasons
 */
namespace OEModule\OphTrConsent\models;
class Element_OphTrConsent_CapacityAssessment extends \BaseEventTypeElement
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return "et_ophtrconsent_capacity_assessment";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array("event_id, how_judgement_was_made, evidence, attempts_to_assist, basis_of_decision, lackOfCapacityReasonIds", "safe"),
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
            'lackOfCapacityReasons' => array(self::MANY_MANY, OphTrConsent_LackOfCapacityReason::class, "et_ophtrconsent_capacity_assessment_lack_cap_reason(lack_of_capacity_reason_id,element_id)")
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            "how_judgement_was_made" => "How were above judgements reached",
            "evidence" => "What evidence has been relied upon",
            "attempts_to_assist" => "What attempts were made to assist the patient to make their own decision and why not successful",
            "basis_of_decision" => "Why patient lacks capacity and the basis for your decision",
            "lackOfCapacityReasons" => "The patient lacks capacity to give or withhold consent to this procedure or course of treatment because of",
            "lackOfCapacityReasonIds" => "The patient lacks capacity to give or withhold consent to this procedure or course of treatment because of",
        );
    }


    public function beforeValidate()
    {
        if (empty($this->lackOfCapacityReasonIds)) {
            $this->addError("lackOfCapacityReasonIds", "Please tick at least one reason or confirm that Patient has capacity to consent");
        }

        return parent::beforeValidate();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function afterSave()
    {
        $saved_reasons = array();
        $existing_models = Element_OphTrConsent_CapacityAssessment_LackCapReason::model()->findAll("element_id = ".$this->id);
        $existing_ids = array_map(function ($e) {
            return $e->lack_of_capacity_reason_id;
        }, $existing_models);

        foreach ($this->lackOfCapacityReasons as $key => $reason) {
            if (!in_array($reason->id, $existing_ids)) {
                $assignment = new Element_OphTrConsent_CapacityAssessment_LackCapReason();
                $assignment->element_id = $this->id;
                $assignment->lack_of_capacity_reason_id = $reason->id;
                $assignment->save();
            }
            $saved_reasons[] = $reason->id;
        }

        foreach ($existing_models as $model) {
            if (!in_array($model->lack_of_capacity_reason_id, $saved_reasons)) {
                $model->delete();
            }
        }

        return parent::afterSave();
    }

    /**
     * Get Lack Of Capacity Reason Ids
     */
    public function getLackOfCapacityReasonIds()
    {
        return array_map(function ($e) {
            return $e->id;
        }, $this->lackOfCapacityReasons);
    }

    /**
     * Sets Lack Of Capacity Reason Ids
     */
    public function setLackOfCapacityReasonIds($ids)
    {
        if ($ids === "") {
            $ids = array();
        }
        $reasons = array();
        foreach ($ids as $id) {
            if ($reason = OphTrConsent_LackOfCapacityReason::model()->findByPk($id)) {
                $reasons[] = $reason;
            }
        }
        $this->lackOfCapacityReasons = $reasons;
    }
}
