<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphTrOperationbooking_Whiteboard extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphTrOperationbooking_Whiteboard the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'booking' => array(
                self::BELONGS_TO,
                'Element_OphTrOperationbooking_Operation',
                '',
                'on' => 't.event_id = booking.event_id',
                'joinType' => 'INNER JOIN',
                'alias' => 'booking',
            ),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id')
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtroperationbooking_whiteboard';
    }

    /**
     * Collate the data and persist it to the table.
     *
     * @param $id
     *
     * @throws CHttpException
     * @throws Exception
     */
    public function loadData($id)
    {
        $booking = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($id));

        $eye = Eye::model()->findByPk($booking->eye_id);
        if ($eye->name === 'Both') {
            throw new CHttpException(400, 'Can\'t display whiteboard for dual eye bookings');
        }
        $eyeLabel = strtolower($eye->name);

        $event = Event::model()->findByPk($id);
        $episode = Episode::model()->findByPk($event->episode_id);
        $patient = Patient::model()->findByPk($episode->patient_id);
        $contact = Contact::model()->findByPk($patient->contact_id);
        $biometry = $this->recentBiometry($patient);
        $blockers = $this->alphaBlockerStatusAndDate($patient);
        $anticoag = $this->anticoagsStatusAndDate($patient);
        $labResult = Element_OphInLabResults_Entry::model()->findPatientResultByType($patient->id, '1');
        $allergyString = $this->allergyString($episode);
        $operation = $this->operation($id);

        $this->event_id = $id;
        $this->booking = $booking;
        $this->eye_id = $eye->id;
        $this->eye = $eye;
        $this->patient_name = $contact['title'] . ' ' . $contact['first_name'] . ' ' . $contact['last_name'];
        $this->date_of_birth = $patient['dob'];
        $this->hos_num = $patient['hos_num'];
        $this->procedure = implode(', ', array_column($operation, 'term'));
        $this->allergies = $allergyString;

        $this->iol_model = 'Unknown';
        $this->iol_power = 'None';
        $this->predicted_refractive_outcome = 'Unknown';

        if ($biometry && in_array($biometry->eye_id, [$booking->eye_id, \EYE::BOTH])) {
            if ($biometry->attributes['lens_display_name_' . $eyeLabel]) {
                $this->iol_model = $biometry->attributes['lens_display_name_' . $eyeLabel] . ' <br> ' . $biometry->attributes['formula_' . $eyeLabel];
                $this->iol_power = $biometry->attributes['iol_power_' . $eyeLabel];
                $this->predicted_refractive_outcome = $biometry->attributes['predicted_refraction_' . $eyeLabel];
            }
        }

        $this->alpha_blockers = $patient->hasRisk('Alpha blockers');
        $this->anticoagulants = $patient->hasRisk('Anticoagulants');
        $this->alpha_blocker_name = $blockers;
        $this->anticoagulant_name = $anticoag;
        $this->inr = ($labResult) ? $labResult : 'None';

        if (!$this->predicted_additional_equipment) {
            $this->predicted_additional_equipment = $booking->special_equipment_details;
        }

        if (!$this->comments) {
            $this->comments = '';
        }

        $this->save();
    }

    /**
     * Is the whiteboard editable.
     *
     * @return bool
     */
    public function isEditable()
    {
        return is_object($this->booking) && $this->booking->isEditable() && !$this->is_confirmed;
    }

    /**
     * @param $patient
     * @return mixed
     */
    protected function recentBiometry($patient)
    {
        $biometryCriteria = new CDbCriteria();
        $biometryCriteria->addCondition('patient_id = :patient_id');
        $biometryCriteria->params = array('patient_id' => $patient->id);
        $biometryCriteria->order = 'last_modified_date DESC';
        $biometryCriteria->limit = 1;
        $biometry = Element_OphTrOperationnote_Biometry::model()->find($biometryCriteria);

        return $biometry;
    }

    /**
     * @param $episode
     *
     * @return string
     */
    protected function allergyString($episode)
    {
        $allergies = Yii::app()->db->createCommand()
            ->select('a.name as name, pas.other as other')
            ->from('patient_allergy_assignment pas')
            ->leftJoin('allergy a', 'pas.allergy_id = a.id')
            ->where("a.name != 'Other' AND pas.patient_id = {$episode->patient_id}")
            ->order('a.name')
            ->queryAll();

        $allergiesOther = Yii::app()->db->createCommand()
            ->select('a.name as name, pas.other as other')
            ->from('patient_allergy_assignment pas')
            ->leftJoin('allergy a', 'pas.allergy_id = a.id')
            ->where("a.name = 'Other' AND pas.patient_id = {$episode->patient_id}")
            ->order('a.name')
            ->queryAll();


        $allergyString = 'None';
        if ($allergies || $allergiesOther) {
            $allergyString = implode(', ', array_column($allergies, 'name'));
            $allergyOtherString = implode(', ', array_column($allergiesOther, 'other'));

            if ($allergyOtherString && $allergyString) {
                $allergyString = $allergyString . ", " . $allergyOtherString;
            }

            if ($allergyOtherString && !$allergyString) {
                $allergyString = $allergyOtherString;
            }


            return $allergyString;
        }

        return $allergyString;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    protected function operation($id)
    {
        $operation = Yii::app()->db->createCommand()
            ->select('proc.term as term')
            ->from('et_ophtroperationbooking_operation op')
            ->leftJoin('ophtroperationbooking_operation_procedures_procedures opp', 'opp.element_id = op.id')
            ->leftJoin('proc', 'opp.proc_id = proc.id')
            ->where("op.event_id = {$id}")
            ->queryAll();

        return $operation;
    }

    /**
     * @param $risk
     * @return string
     */
    private function getDisplayHasRisk($risk)
    {
        //$risk['status'] can be true/false/null

        $status = 'Not checked';

        if ($risk['status'] === true) {
            $status = 'Present';
        };
        if ($risk['status'] === false) {
            $status = 'Not present';
        };

        return $status;
    }
    
    /**
     * @param $patient
     *
     * @return string
     */
    protected function alphaBlockerStatusAndDate($patient)
    {
        $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
        if ($exam_api) {
            $alpha = $exam_api->getRiskByName($patient, 'Alpha blockers');
            if ($alpha) {
                return $this->getDisplayHasRisk($alpha) . ($alpha['comments'] ? ' - ' . $alpha['comments'] : '') . '(' . Helper::convertMySQL2NHS($alpha['date']) . ')';
            }
        }

        //default value when no Risk element exists
        return 'Not checked';
    }

    /**
     * @param $patient
     *
     * @return string
     */
    protected function anticoagsStatusAndDate($patient)
    {
        $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
        if ($exam_api) {
            $anticoag = $exam_api->getRiskByName($patient, 'Anticoagulants');
            if ($anticoag) {
                return $this->getDisplayHasRisk($anticoag) . ($anticoag['comments'] ? ' - ' . $anticoag['comments'] : '') . '(' . Helper::convertMySQL2NHS($anticoag['date']) . ')';
            }
        }

        //default value when no Risk element exists
        return 'Not checked';
    }

    /**
     * @return string
     */

    public function getPatientRisksDisplay()
    {
        /** @var Patient $patient */
        $patient = $this->event->patient;
        $lines = array();

        // Search for diabetes

        $diabetic_disorders = $patient->getDisordersOfType(Disorder::$SNOMED_DIABETES_SET);

        if (!empty($diabetic_disorders)) {
            foreach ($diabetic_disorders as $disorder) {
                $lines[] = $disorder;
            }
        }

        // Check risks

        $risks = $patient->riskAssignments;

        // Exclude anti-coags and alpha-blockers as they've been called out in their respective sections already

        $risks = array_filter($risks, function($risk){
            return !in_array($risk->name, ["Anticoagulants", "Alpha blockers"]);
        });

        $lines = array_merge($lines, array_map(function($risk){
            if ($risk->comments != "") {
                return '<span class="has-tooltip" data-tooltip-content="'.$risk->comments.'">'.$risk->name.'</span>';
            }
            return $risk->name;
        }, $risks));

        $display = implode('<br/>', $lines);

        return $display === "" ? "None" : $display;
    }
}
