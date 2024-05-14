<?php

use OEModule\OphCiExamination\models\OphCiExaminationRisk;

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
    private $procedure_short_format_threshold = 64;
    /**
     * Returns the static model of the specified AR class.
     * @param $className string
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
            'biometry_report' => array(
                self::BELONGS_TO,
                'Element_OphCoDocument_Document',
                'biometry_report_id',
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
        if ($eye->name === 'Both' && $booking->event->episode->firm->getSubspecialty()->name === 'Cataract') {
            throw new CHttpException(400, 'Can\'t display whiteboard for dual eye bookings');
        }
        $eyeLabel = strtolower($eye->name);

        $event = Event::model()->findByPk($id);
        $episode = Episode::model()->findByPk($event->episode_id);
        $patient = Patient::model()->findByPk($episode->patient_id);
        $contact = Contact::model()->findByPk($patient->contact_id);

        $biometry = $this->recentBiometry($patient);
        $report = $this->recentBiometryReport($patient);
        $blockers = $this->alphaBlockerStatusAndDate($patient);
        $anticoag = $this->anticoagsStatusAndDate($patient);

        $operation = $this->operation($id);
        $procedures_text = implode(', ', array_column($operation, 'term'));
        if (strlen($procedures_text) > $this->procedure_short_format_threshold) {
            $procedures_text = [];
            foreach ($operation as $procedure) {
                if (!empty($procedure['short_term'])) {
                    $procedures_text[] = $procedure['short_term'];
                } else {
                    $procedures_text[] = $procedure['term'];
                }
            }
            $procedures_text = implode(', ', $procedures_text);
        }
        $allergyString = $this->allergyString($episode);

        $this->event_id = $id;
        $this->booking = $booking;
        $this->eye_id = $eye->id;
        $this->eye = $eye;
        $this->patient_name = $contact->title . ' ' . $contact->first_name . ' ' . $contact->last_name;
        $this->date_of_birth = $patient['dob'];
        $this->hos_num = $patient['hos_num'];
        $this->procedure = $procedures_text;
        $this->allergies = $allergyString;
        $this->complexity = $booking->complexity;

        if ($report) {
            $this->biometry_report = $report;
        }

        $this->iol_model = 'Unknown';
        $this->iol_power = 'None';
        $this->axial_length = 'Unknown';
        $this->acd = 'Unknown';
        $this->predicted_refractive_outcome = 0.0;
        $this->formula = 'Unknown';
        $this->axis = 0.0;

        if ($biometry && in_array($biometry->eye_id, [$booking->eye_id, EYE::BOTH])) {
                $this->iol_model = $biometry->attributes["lens_display_name_$eyeLabel"];
                $this->iol_power = $biometry->attributes["iol_power_$eyeLabel"];
                $this->axial_length = $biometry->attributes["axial_length_$eyeLabel"];
                $this->acd = $biometry->attributes["acd_$eyeLabel"];
                $this->predicted_refractive_outcome = $biometry->attributes["predicted_refraction_$eyeLabel"];
                $this->formula = $biometry->attributes["formula_$eyeLabel"];
                $this->aconst = $biometry->attributes["lens_acon_$eyeLabel"];
                $this->axis = $biometry->attributes["k1_$eyeLabel"] > $biometry->attributes["k2_$eyeLabel"] ? $biometry->attributes["k1_axis_$eyeLabel"] : $biometry->attributes["k2_axis_$eyeLabel"];
                $this->flat_k = $biometry->attributes["k1_$eyeLabel"];
                $this->steep_k = $biometry->attributes["k2_$eyeLabel"];
        }

        $this->alpha_blockers = $patient->hasRisk('Alpha blockers');
        $this->anticoagulants = $patient->hasRisk('Anticoagulants');
        $this->alpha_blocker_name = $blockers;
        $this->anticoagulant_name = $anticoag;

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
     * @return string
     * @throws CException
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
                $allergyString .= ', ' . $allergyOtherString;
            }

            if ($allergyOtherString && !$allergyString) {
                $allergyString = $allergyOtherString;
            }

            return $allergyString;
        }

        if (!$episode->patient->no_allergies_date) {
            $allergyString = 'Unknown';
        }

        return $allergyString;
    }

    /**
     * @param $id
     * @return mixed
     * @throws CException
     */
    protected function operation($id)
    {
        $operation = Yii::app()->db->createCommand()
            ->select('proc.term as term, proc.short_format as short_term')
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

        if (isset($risk)) {
            if ($risk['status'] === true) {
                $status = 'Present';
            }
            if ($risk['status'] === false) {
                $status = 'Not present';
            }
        }

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
            if ($alpha && $this->getDisplayHasRisk($alpha) === 'Present') {
                return ($alpha['comments'] ?: '') . '(' . Helper::convertMySQL2NHS($alpha['date']) . ')';
            } elseif (!$alpha || $this->getDisplayHasRisk($alpha) === 'Not checked') {
                return 'Not checked';
            }
            return 'No Alpha Blockers';
        }

        //default value when no Risk element exists
        return 'Not checked';
    }

    /**
     * @param $patient Patient
     * @return Element_OphCoDocument_Document|null
     */
    protected function recentBiometryReport($patient)
    {
        $biometry_report_subtype = OphCoDocument_Sub_Types::model()->findByAttributes(array('name' => 'Biometry Report'));

        $criteria = new CDbCriteria();
        $criteria->with = array('event.episode.patient');
        $criteria->addCondition('patient_id = :patient_id');
        $criteria->addCondition('event_sub_type = :sub_type');
        $criteria->params = array('patient_id' => $patient->id, 'sub_type' => $biometry_report_subtype->id);
        $criteria->order = 't.last_modified_date DESC';
        $criteria->limit = 1;

        $recent_document = Element_OphCoDocument_Document::model()->find($criteria);

        $criteria = new CDbCriteria();
        $criteria->with = array('event.episode.patient');
        $criteria->join = "JOIN event ev ON t.event_id = ev.id";
        $criteria->addCondition('episode.patient_id = :patient_id');
        $criteria->join .= " RIGHT JOIN event_attachment_group eag ON eag.event_id = ev.id";
        $criteria->params = array('patient_id' => $patient->id);
        $criteria->order = 'ev.event_date DESC';
        $criteria->limit = 1;

        $recent_attachment_document = OphInBiometry_Imported_Events::model()->find($criteria);

        if ($recent_document === null && $recent_attachment_document === null) {
            return null;
        }

        if ($recent_document !== null && $recent_attachment_document === null) {
            return $recent_document;
        } elseif ($recent_document === null && $recent_attachment_document !== null) {
            return $recent_attachment_document;
        } else {
            return $recent_document->last_modified_date > $recent_attachment_document->last_modified_date ?
                $recent_document :
                $recent_attachment_document;
        }
    }

    /**
     * @param $patient
     *
     * @return string
     */
    protected function anticoagsStatusAndDate($patient)
    {
        $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
        $labResult = Element_OphInLabResults_Entry::model()->findPatientResultByType($patient->id, '1');
        $this->inr = ($labResult) ? $labResult : 'None';
        if ($exam_api) {
            $anticoag = $exam_api->getRiskByName($patient, 'Anticoagulants');
            if ($anticoag && $this->getDisplayHasRisk($anticoag) == 'Present') {
                return ($anticoag['comments'] ?: '') . '(' . ($this->inr !== 'None' ? "INR {$this->inr}, " : '')
                    . Helper::convertMySQL2NHS($anticoag['date']) . ')';
            } elseif (!$anticoag || $this->getDisplayHasRisk($anticoag) === 'Not checked') {
                return 'Not checked';
            }
            return 'No Anticoagulants';
        }

        //default value when no Risk element exists
        return 'Not checked';
    }

    public function getPatientLabResultsDisplay()
    {
        $patient = $this->event->patient;

        $criteria = new CDbCriteria();
        $criteria->join = 'JOIN event e ON t.event_id = e.id ';
        $criteria->join .= 'JOIN episode ep ON e.episode_id = ep.id';
        $criteria->compare('ep.patient_id', $patient->id);

        $lab_results = Element_OphInLabResults_ResultTimedNumeric::model()->findAll($criteria);

        $lines = array_map(
            static function ($lab_result) {
                $unit = $lab_result->unit ? $lab_result->unit : '';
                return [
                    'type' => $lab_result->resultType->type,
                    'date' => Helper::convertMySQL2NHS($lab_result->event->event_date) . ' at ' . $lab_result->time,
                    'result' => $lab_result->result . " {$unit}",
                    'comment' => $lab_result->comment
                ];
            },
            array_filter(
                $lab_results,
                static function ($lab_result) {
                    return $lab_result->resultType->show_on_whiteboard;
                }
            )
        );

        $display = '';

        foreach ($lines as $line) {
            $display .= "<div class='alert-box warning'>" .
                $line['type'] .
                "<div>{$line['date']}</div>" .
                $line['result'] . '<br>' .
                "<span class='user-comment'>{$line['comment']}</span>" .
                "</div>";
        }

        return $display;
    }

    /**
     * @param $total_risks int Total risks for the patient. The variable passed to this function is populated with the value.
     * @return string
     */
    public function getPatientRisksDisplay(&$total_risks)
    {
        /** @var Patient $patient */
        $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
        $patient = $this->event->patient;
        $lines = array();
        $whiteboard = $this;

        // Search for diabetes
        $diabetic_disorders = $patient->getDisordersOfType(Disorder::$SNOMED_DIABETES_SET);

        if (!empty($diabetic_disorders)) {
            foreach ($diabetic_disorders as $disorder) {
                $lines[] = array('Present', $disorder);
            }
        }

        // Check risks

        $risks = $patient->riskAssignments;

        $anticoag = array_filter($risks, static function ($risk) {
            return $risk->name === 'Anticoagulants';
        });

        // Exclude anti-coags and alpha-blockers as they've been called out in their respective sections already

        $risks = array_filter($risks, static function ($risk) {
            return $risk->name !== 'Anticoagulants';
        });

        $other_risk = OphCiExaminationRisk::model()->findByAttributes(array('name' => 'Other'));

        $lines = array_merge(
            $lines,
            array_map(
                static function ($risk) use ($exam_api, $patient, $whiteboard, $other_risk) {
                    $risk_name = $risk->name;

                    if ($risk->risk_id === $other_risk->id) {
                        $risk_name = 'Other';
                    }
                    $exam_risk = $exam_api->getRiskByName($patient, $risk_name);
                    $risk_present = $whiteboard->getDisplayHasRisk($exam_risk);

                    if ($risk->name === 'Alpha blockers') {
                        return array($risk_present, 'Alphablocker', $whiteboard->alphaBlockerStatusAndDate($patient));
                    }

                    if ($risk->comments !== '') {
                        if ($exam_risk['name'] === 'Other') {
                            return array($risk_present, '<span class="has-tooltip" data-tooltip-content="' . $risk->comments . '">' . $risk->other . '</span>');
                        }
                        return array($risk_present, '<span class="has-tooltip" data-tooltip-content="' . $risk->comments . '">' . $exam_risk['name'] . '</span>');
                    }
                    if ($exam_risk['name'] === 'Other') {
                        return array($risk_present, $risk->other);
                    }
                    return array($risk_present, $exam_risk['name']);
                },
                array_filter(
                    $risks,
                    static function ($risk) {
                        return $risk->display_on_whiteboard;
                    }
                )
            )
        );

        $display = '';

        foreach ($lines as $line) {
            if ($line[0] === 'Present') {
                $total_risks++;
                $line_display = '';
                for ($i = 1; $i < count($line); ++$i) {
                    $line_display .= $line[$i] . '<br>';
                }
                $display .= '<div class="alert-box warning">' . $line_display . '</div>';
            }
        }

        if (!$patient->no_risks_date
            && !$risks
            && empty($anticoag)
            && $this->anticoagulant_name !== 'No Anticoagulants') {
            $total_risks = 0;
            $display .= '<div class="alert-box info">Status unknown</div>';
        }

        // Add positive/unknown risk labels for significant risks that are not present or are unchecked.
        // Anticoagulants and alpha blockers are excluded from this list as they are handled independently.
        foreach ($this->booking->getAllBookingRisks() as $risk) {
            if ($risk->name !== 'Anticoagulants') {
                $exam_risk = $exam_api->getRiskByName($patient, $risk->name);
                $has_risk = $this->getDisplayHasRisk($exam_risk);
                if ($has_risk === 'Not checked') {
                    $display .= '<div class="alert-box info">' . "Unchecked: {$risk->name}" . '</div>';
                } elseif ($has_risk === 'Not present') {
                    $display .= '<div class="alert-box success">' . "Absent: {$risk->name}" . '</div>';
                }
                // Do not display anything if the risk is present.
            }
        }

        return $display;
    }
}
