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
class OphTrOperationnote_ReportOperations extends BaseReport
{
    public $surgeon_id;
    public $Procedures_procs;
    public $complications;
    public $date_from;
    public $date_to;
    public $bookingcomments;
    public $booking_diagnosis;
    public $surgerydate;
    public $theatre;
    public $comorbidities;
    public $first_eye;
    public $refraction_values;
    public $target_refraction;
    public $cataract_surgical_management;
    public $va_values;
    public $cataract_report;
    public $incision_site;
    public $cataract_complication_notes;
    public $cataract_predicted_refraction;
    public $cataract_iol_type;
    public $cataract_iol_power;
    public $tamponade_used;
    public $anaesthetic_type;
    public $anaesthetic_delivery;
    public $anaesthetic_complications;
    public $anaesthetic_comments;
    public $surgeon;
    public $surgeon_role;
    public $assistant;
    public $assistant_role;
    public $supervising_surgeon;
    public $supervising_surgeon_role;
    public $opnote_comments;
    public $patient_oph_diagnoses;
    public $operations;
    public $operation_date;

    public function attributeNames()
    {
        return array(
            'surgeon_id',
            'Procedures_procs',
            'complications',
            'date_from',
            'date_to',
            'bookingcomments',
            'booking_diagnosis',
            'surgerydate',
            'theatre',
            'comorbidities',
            'first_eye',
            'refraction_values',
            'target_refraction',
            'cataract_surgical_management',
            'va_values',
            'cataract_report',
            'incision_site',
            'cataract_complication_notes',
            'tamponade_used',
            'anaesthetic_type',
            'anaesthetic_delivery',
            'anaesthetic_complications',
            'anaesthetic_comments',
            'surgeon',
            'surgeon_role',
            'assistant',
            'assistant_role',
            'supervising_surgeon',
            'supervising_surgeon_role',
            'opnote_comments',
            'patient_oph_diagnoses',
            'institution_id'
        );
    }

    public function attributeLabels()
    {
        return array(
            'surgeon_id' => 'Surgeon',
            'Procedures_procs' => 'Procedures',
            'complications' => 'Cataract complications',
            'date_from' => 'Date from',
            'date_to' => 'Date to',
            'bookingcomments' => 'Booking comments',
            'booking_diagnosis' => 'Operation booking diagnosis',
            'surgerydate' => 'Surgery date',
            'theatre' => 'Theatre',
            'comorbidities' => 'Comorbidities',
            'first_eye' => 'First or second eye',
            'refraction_values' => 'Refraction values',
            'target_refraction' => 'Target refraction',
            'cataract_surgical_management' => 'Cataract Surgical Management',
            'va_values' => 'VA values',
            'cataract_report' => 'Cataract report',
            'incision_site' => 'Incision Site',
            'cataract_complication_notes' => 'Cataract Complication Notes',
            'tamponade_used' => 'Tamponade used',
            'anaesthetic_type' => 'Anaesthetic type',
            'anaesthetic_delivery' => 'Anaesthetic delivery',
            'anaesthetic_complications' => 'Anaesthetic complications',
            'anaesthetic_comments' => 'Anaesthetic comments',
            'surgeon' => 'Surgeon',
            'surgeon_role' => 'Surgeon role',
            'assistant' => 'Assistant',
            'assistant_role' => 'Assistant role',
            'supervising_surgeon' => 'Supervising surgeon',
            'supervising_surgeon_role' => 'Supervising surgeon role',
            'opnote_comments' => 'Operation note comments',
            'patient_oph_diagnoses' => 'Patient ophthalmic diagnoses',
            'all_ids' => 'Patient IDs',
        );
    }

    public function rules()
    {
        return array(
            array(implode(',', $this->attributeNames()), 'safe'),
            array('date_from, date_to', 'required'),
        );
    }

    public function run()
    {
        $this->setInstitutionAndSite();

        $surgeon = null;
        $date_from = date('Y-m-d', strtotime('-1 year'));
        $date_to = date('Y-m-d');

        if ( !Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id) ) {
            $this->surgeon_id = Yii::app()->user->id;
        }

        if ($this->surgeon_id) {
            $surgeon_id = (int) $this->surgeon_id;

            if (!$surgeon = User::model()->findByPk($surgeon_id)) {
                throw new CException("Unknown surgeon $surgeon_id");
            }
        }
        if ($this->date_from && strtotime($this->date_from)) {
            $date_from = date('Y-m-d', strtotime($this->date_from));
        }
        if ($this->date_to && strtotime($this->date_to)) {
            $date_to = date('Y-m-d', strtotime($this->date_to));
        }
        $filter_procedures = null;
        if ($this->Procedures_procs) {
            $filter_procedures = $this->Procedures_procs;
        }
        $filter_complications = null;
        if ($this->complications) {
            $filter_complications = $this->complications;
        }

        // ensure we don't hit PAS
        Yii::app()->event->dispatch('start_batch_mode');

        $this->operations = $this->getOperations(
            $filter_procedures,
            $filter_complications,
            $date_from,
            $date_to,
            $this->patient_oph_diagnoses,
            $this->booking_diagnosis,
            $this->theatre,
            $this->bookingcomments,
            $this->surgerydate,
            $this->comorbidities,
            $this->target_refraction,
            $this->cataract_surgical_management,
            $this->first_eye,
            $this->va_values,
            $this->refraction_values,
            $this->anaesthetic_type,
            $this->anaesthetic_delivery,
            $this->anaesthetic_comments,
            $this->anaesthetic_complications,
            $this->cataract_report,
            $this->incision_site,
            $this->cataract_complication_notes,
            $this->cataract_predicted_refraction,
            $this->cataract_iol_type,
            $this->cataract_iol_power,
            $this->tamponade_used,
            $this->surgeon,
            $this->surgeon_role,
            $this->assistant,
            $this->assistant_role,
            $this->supervising_surgeon,
            $this->supervising_surgeon_role,
            $this->opnote_comments,
            $this->surgeon_id
        );

        Yii::app()->event->dispatch('end_batch_mode');
    }

    /**
     * Generate operation report.
     *
     * @param User  $surgeon
     * @param array $filter_procedures
     * @param array $filter_complications
     * @param $from_date
     * @param $to_date
     * @param array $appenders - list of methods to call with patient id and date to retrieve additional data for each row
     *
     * @return array
     */
    protected function getOperations($filter_procedures = array(), $filter_complications = array(), $from_date, $to_date, $patient_oph_diagnoses, $booking_diagnosis, $theatre, $bookingcomments, $surgerydate, $comorbidities, $target_refraction, $cataract_surgical_management, $first_eye, $va_values, $refraction_values, $anaesthetic_type, $anaesthetic_delivery, $anaesthetic_comments, $anaesthetic_complications, $cataract_report, $incision_site, $cataract_complication_notes, $cataract_predicted_refraction, $cataract_iol_type, $cataract_iol_power, $tamponade_used, $surgeon, $surgeon_role, $assistant, $assistant_role, $supervising_surgeon, $supervising_surgeon_role, $opnote_comments, $surgeon_id)
    {
        $filter_procedures_method = 'OR';
        $filter_complications_method = 'OR';

        $command = Yii::app()->db->createCommand()
            ->select(
                'e.id, c.first_name, c.last_name, e.event_date, su.surgeon_id, su.assistant_id, su.supervising_surgeon_id, p.id as pid, p.gender, p.dob, pl.id as plid, cat.id as cat_id, eye.name AS eye'
            )
            ->from('event e')
            ->join('episode ep', 'e.episode_id = ep.id')
            ->join('patient p', 'ep.patient_id = p.id')
            ->join('et_ophtroperationnote_procedurelist pl', 'pl.event_id = e.id')
            ->join('et_ophtroperationnote_surgeon su', 'su.event_id = e.id')
            ->join('contact c', 'p.contact_id = c.id')
            ->join('eye', 'eye.id = pl.eye_id')
            ->leftJoin('et_ophtroperationnote_cataract cat', 'cat.event_id = e.id')
            ->where('e.deleted = 0 and ep.deleted = 0 and e.event_date >= :from_date and e.event_date < :to_date + interval 1 day')
            ->order('p.id, e.event_date asc, e.created_date asc');
        $params = array(':from_date' => $from_date, ':to_date' => $to_date);

        if ($surgeon_id) {
            $command->andWhere(
                '(su.surgeon_id = :user_id or su.assistant_id = :user_id or su.supervising_surgeon_id = :user_id)'
            );
            $params[':user_id'] = $surgeon_id;
        }

        if ($this->institution_id) {
            $command->andWhere('e.institution_id = :institution_id');
            $params[':institution_id'] = $this->institution_id;
        }

        $results = array();
        $cache = array();
        foreach ($command->queryAll(true, $params) as $row) {
            set_time_limit(1);
            $complications = array();
            if ($row['cat_id']) {
                foreach (OphTrOperationnote_CataractComplication::model()->findAll('cataract_id = ?', array($row['cat_id'])) as $complication) {
                    if (!isset($cache['complications'][$complication->complication_id])) {
                        $cache['complications'][$complication->complication_id] = $complication->complication->name;
                    }
                    $complications[(string) $complication->complication_id] = $cache['complications'][$complication->complication_id];
                }
            }

            $matched_complications = 0;
            if ($filter_complications) {
                foreach ($filter_complications as $filter_complication) {
                    if (isset($complications[$filter_complication])) {
                        ++$matched_complications;
                    }
                }
                if (($filter_complications_method == 'AND' && $matched_complications < count(
                    $filter_complications
                )) || !$matched_complications
                ) {
                    continue;
                }
            }

            $procedures = array();
            foreach (OphTrOperationnote_ProcedureListProcedureAssignment::model()->findAll('procedurelist_id = ?', array($row['plid'])) as $pa) {
                if (!isset($cache['procedures'][$pa->proc_id])) {
                    $cache['procedures'][$pa->proc_id] = $pa->procedure->term;
                }
                $procedures[(string) $pa->proc_id] = $cache['procedures'][$pa->proc_id];
            }
            $matched_procedures = 0;
            if ($filter_procedures) {
                foreach ($filter_procedures as $filter_procedure) {
                    if (isset($procedures[$filter_procedure])) {
                        ++$matched_procedures;
                    }
                }
                if (($filter_procedures_method == 'AND' && $matched_procedures < count(
                    $filter_procedures
                )) || !$matched_procedures
                ) {
                    continue;
                }
            }

            $patient_identifier = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $row['pid'], $this->user_institution_id, $this->user_selected_site_id));
            $patient_identifiers = PatientIdentifierHelper::getAllPatientIdentifiersForReports($row['pid']);

            $record = array(
                'operation_date' => date('j M Y', strtotime($row['event_date'])),
                'patient_identifier' => $patient_identifier,
                'patient_firstname' => $row['first_name'],
                'patient_surname' => $row['last_name'],
                'patient_gender' => $row['gender'],
                'patient_dob' => date('j M Y', strtotime($row['dob'])),
                'eye' => $row['eye'],
                'procedures' => implode(', ', $procedures),
                'complications' => implode(', ', $complications),
            );

            $this->operation_date = strtotime($row['event_date']);

            if ($surgeon_id) {
                if ($row['surgeon_id'] == $surgeon_id) {
                    $record['role'] = 'Surgeon';
                } else {
                    if ($row['assistant_id'] == $surgeon_id) {
                        $record['role'] = 'Assistant surgeon';
                    } else {
                        if ($row['supervising_surgeon_id'] == $surgeon_id) {
                            $record['role'] = 'Supervising surgeon';
                        }
                    }
                }
            }

            //appenders
            $this->appendPatientValues($record, $row['id'], $patient_oph_diagnoses);
            $this->appendBookingValues($record, $row['id'], $booking_diagnosis, $theatre, $bookingcomments, $surgerydate);
            $this->appendOpNoteValues($record, $row['id'], $anaesthetic_type, $anaesthetic_delivery, $anaesthetic_comments, $anaesthetic_complications, $cataract_report, $incision_site, $cataract_complication_notes, $cataract_predicted_refraction, $cataract_iol_type, $cataract_iol_power, $tamponade_used, $surgeon, $surgeon_role, $assistant, $assistant_role, $supervising_surgeon, $supervising_surgeon_role, $opnote_comments);
            $this->appendExaminationValues($record, $row['id'], $comorbidities, $target_refraction, $cataract_surgical_management, $first_eye, $va_values, $refraction_values);

            $record['all_ids'] = $patient_identifiers;

            $results[] = $record;
        }

        return $results;
    }

    protected function appendPatientValues(&$record, $event_id, $patient_oph_diagnoses)
    {
        $event = Event::model()->findByPk($event_id);
        $patient = $event->episode->patient;
        if ($patient_oph_diagnoses) {
            $diagnoses = array();
            foreach ($patient->episodes as $ep) {
                if ($ep->diagnosis) {
                    $diagnoses[] = (($ep->eye) ? $ep->eye->adjective.' ' : '').$ep->diagnosis->term;
                }
            }
            foreach ($patient->getOphthalmicDiagnoses() as $sd) {
                $diagnoses[] = $sd->eye->adjective.' '.$sd->disorder->term;
            }
            $record['patient_diagnoses'] = implode(', ', $diagnoses);
        }
    }

    protected function appendBookingValues(&$record, $event_id, $booking_diagnosis, $theatre, $bookingcomments, $surgerydate)
    {
        if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
            $procedure = Element_OphTrOperationnote_ProcedureList::model()->find('event_id=:event_id', array(':event_id' => $event_id));
            $bookingEventID = $procedure['booking_event_id'];
            foreach (array('booking_diagnosis', 'theatre', 'bookingcomments', 'surgerydate') as $k) {
                if (${$k}) {
                    $record[$k] = '';
                }
            }

            if ($bookingEventID) {
                $operationElement = $api->getOperationForEvent($bookingEventID);
                $latestBookingID = $operationElement['latest_booking_id'];
                $operationBooking = OphTrOperationbooking_Operation_Booking::model()->find('id=:id', array('id' => $latestBookingID));

                if ($booking_diagnosis) {
                    $diag_el = $operationElement->getDiagnosis();
                    $disorder = $diag_el->disorder();
                    if ($disorder) {
                        $record['booking_diagnosis'] = $diag_el->eye->adjective.' '.$disorder->term;
                    } else {
                        $record['booking_diagnosis'] = 'Unknown';
                    }
                }

                if ($this->theatre && $operationElement && $operationBooking) {
                    $theatreName = $operationElement->site['name'].' '.$operationBooking->theatre['name'];
                    $record['theatre'] = $theatreName;
                }

                if ($operationElement && $this->bookingcomments) {
                    $record['bookingcomments'] = $operationElement['comments'];
                }

                if ($operationBooking && $this->surgerydate) {
                    $record['surgerydate'] = $operationBooking['session_date'];
                }
            }
        }
    }

    protected function appendExaminationValues(&$record, $event_id, $comorbidities, $target_refraction, $cataract_surgical_management, $first_eye, $va_values, $refraction_values)
    {
        $event = Event::model()->with('episode')->findByPk($event_id);

        if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {
            $preOpCriteria = $this->preOperationNoteCriteria($event);
            $postOpCriteria = $this->postOperationNoteCriteria($event);

            if ($this->comorbidities) {
                $record['comorbidities'] = $this->getComorbidities($preOpCriteria);
            }

            if ($this->first_eye) {
                $record['first_or_second_eye'] = $this->getFirstEyeOrSecondEye($preOpCriteria);
            }

            if ($this->refraction_values) {
                $record['pre-op refraction'] = $this->getRefractionReading($preOpCriteria, $record);
                $split_preop_refraction = $this->getRefractionReadingSplit($preOpCriteria, $record);
                $record['Pre-op sphere'] = 'Unknown';
                $record['Pre-op cylinder'] = 'Unknown';
                $record['Pre-op axis'] = 'Unknown';
                $record['Pre-op type'] = 'Unknown';
                $record['Pre-op Spherical equivalent'] = 'Unknown';
                if ($split_preop_refraction) {
                    $record['Pre-op sphere'] = $split_preop_refraction['sphere'];
                    $record['Pre-op cylinder'] = $split_preop_refraction['cylinder'];
                    $record['Pre-op axis'] = $split_preop_refraction['axis'];
                    $record['Pre-op type'] = $split_preop_refraction['type'];
                    $record['Pre-op Spherical equivalent'] = number_format($split_preop_refraction['sphere'] + 0.5 * $split_preop_refraction['cylinder'], 2);
                }

                $latest_refraction = $this->getPostOpRefractionSplit($postOpCriteria, $record);
                $record['Post-op Refraction (2-6 weeks) date'] = 'Unknown';
                $record['Post-op 2-6 weeks sphere'] = 'Unknown';
                $record['Post-op 2-6 weeks cylinder'] = 'Unknown';
                $record['Post-op 2-6 weeks axis'] = 'Unknown';
                $record['Post-op 2-6 weeks type'] = 'Unknown';
                $record['Post-op 2-6 weeks Spherical equivalent'] = 'Unknown';
                if ($latest_refraction) {
                    $record['Post-op Refraction (2-6 weeks) date'] = $latest_refraction['date'];
                    $record['Post-op 2-6 weeks sphere'] = $latest_refraction['sphere'];
                    $record['Post-op 2-6 weeks cylinder'] = $latest_refraction['cylinder'];
                    $record['Post-op 2-6 weeks axis'] = $latest_refraction['axis'];
                    $record['Post-op 2-6 weeks type'] = $latest_refraction['type'];
                    $record['Post-op 2-6 weeks Spherical equivalent'] = number_format($latest_refraction['sphere'] + 0.5 * $latest_refraction['cylinder'], 2);
                }

                $record['most recent post-op refraction'] = $this->getRefractionReading($postOpCriteria, $record);
                $split_postop_refraction = $this->getRefractionReadingSplit($postOpCriteria, $record);
                $record['Post-op sphere'] = 'Unknown';
                $record['Post-op cylinder'] = 'Unknown';
                $record['Post-op axis'] = 'Unknown';
                $record['Post-op type'] = 'Unknown';
                $record['Post-op Spherical equivalent'] = 'Unknown';
                if ($split_postop_refraction) {
                    $record['Post-op sphere'] = $split_postop_refraction['sphere'];
                    $record['Post-op cylinder'] = $split_postop_refraction['cylinder'];
                    $record['Post-op axis'] = $split_postop_refraction['axis'];
                    $record['Post-op type'] = $split_postop_refraction['type'];
                    $record['Post-op Spherical equivalent'] = number_format($split_postop_refraction['sphere'] + 0.5 * $split_postop_refraction['cylinder'], 2);
                }
            }

            if ($this->target_refraction) {
                $record['target_refraction'] = $this->getTargetRefraction($preOpCriteria);
            }

            if ($this->cataract_surgical_management) {
                $record['Post Op Refractive Target Discussed With Patient'] = 'Unknown';
                $record['Previous Refractive Surgery'] = 'Unknown';
                $record['Vitrectomised Eye'] = 'Unknown';
                $record['Primary reason for cataract surgery'] = 'Unknown';
                $csm = $this->getCataractSurgicalManagement($preOpCriteria);

                if ($csm) {
                    if ($csm['correction_discussed']) {
                        $record['Post Op Refractive Target Discussed With Patient'] = ($csm['correction_discussed'] == 1) ? 'Yes' : 'No';
                    }

                    if ($csm['previous_refractive_surgery']) {
                        $record['Previous Refractive Surgery'] = ($csm['previous_refractive_surgery'] == 1) ? 'Yes' : 'No';
                    }

                    if ($csm['vitrectomised_eye']) {
                        $record['Vitrectomised Eye'] = ($csm['vitrectomised_eye'] == 1) ? 'Yes' : 'No';
                    }
                }

                $reason = $this->getCataractPrimaryReason($preOpCriteria);
                if ($reason != '') {
                    $record['Primary reason for cataract surgery'] = $reason;
                }
            }

            if ($this->va_values) {
                $record['Pre-op VA Date'] = $this->getVAReadingDate($preOpCriteria, $record);
                $record['pre-op va'] = $this->getVaReading($preOpCriteria, $record);
                $split_preop_values = $this->getVaReadingSplit($preOpCriteria, $record);
                $record['Pre-op VA Glasses'] = 'Unknown';
                $record['Pre-op VA Unaided'] = 'Unknown';
                $record['Pre-op VA Pinhole'] = 'Unknown';
                foreach ($split_preop_values as $split_preop_value) {
                    if ($split_preop_value['va_reading'] != '' && in_array($split_preop_value['method'], array('Glasses', 'Unaided', 'Pinhole'))) {
                        $index = 'Pre-op VA '.$split_preop_value['method'];
                        $record[$index] = $split_preop_value['va_reading'];
                    }
                }

                $best_post_ops = $this->bestPostOpVaValues($postOpCriteria, $record);
                $record['Post-op VA (2-6 weeks) date'] = 'Unknown';
                $record['2-6 Week value Glasses'] = 'Unknown';
                $record['2-6 Week value Unaided'] = 'Unknown';
                $record['2-6 Week value Pinhole'] = 'Unknown';

                foreach ($best_post_ops as $best_post_op) {
                    if ($best_post_op && in_array($best_post_op['method'], array('Glasses', 'Unaided', 'Pinhole'))) {
                        $record['Post-op VA (2-6 weeks) date'] = $best_post_op['date'];
                        $record['2-6 Week value '.$best_post_op['method']] = $best_post_op['reading'];
                    }
                }

                $record['most recent post-op va'] = $this->getVaReading($postOpCriteria, $record);
                $split_postop_values = $this->getVaReadingSplit($postOpCriteria, $record);
                $record['Most recent post-op Glasses'] = 'Unknown';
                $record['Most recent post-op Unaided'] = 'Unknown';
                $record['Most recent post-op Pinhole'] = 'Unknown';
                foreach ($split_postop_values as $split_postop_value) {
                    if ($split_postop_value['va_reading'] != '' && in_array($split_postop_value['method'], array('Glasses', 'Unaided', 'Pinhole'))) {
                        $index = 'Most recent post-op '.$split_postop_value['method'];
                        $record[$index] = $split_postop_value['va_reading'];
                    }
                }
            }
        }
    }

    protected function preOperationNoteCriteria($event)
    {
        return $this->operationNoteCriteria($event, true);
    }

    public function postOperationNoteCriteria($event)
    {
        return $this->operationNoteCriteria($event, false);
    }

    public function operationNoteCriteria($event, $searchBackwards)
    {
        $criteria = new CDbCriteria();
        if ($searchBackwards) {
            $criteria->addCondition('event.event_date < :op_date');
        } else {
            $criteria->addCondition('event.event_date > :op_date');
        }
        $criteria->addCondition('event.deleted = 0');
        $criteria->addCondition('event.episode_id = :episode_id');
        $criteria->params[':episode_id'] = $event->episode_id;
        $criteria->params[':op_date'] = $event->event_date;
        $criteria->order = 'event.event_date desc, event.created_date desc';
        $criteria->limit = 1;

        return $criteria;
    }

    protected function eyesCondition($record)
    {
        if (strtolower($record['eye']) == 'left') {
            $eyes = array(Eye::LEFT, Eye::BOTH);
        } else {
            $eyes = array(Eye::RIGHT, Eye::BOTH);
        }

        return $eyes;
    }

    protected function getComorbidities($criteria)
    {
        $comorbiditiesElement = \OEModule\OphCiExamination\models\Element_OphCiExamination_Comorbidities::model()->with(array('event'))->find($criteria);

        $comorbidities = array();
        if (isset($comorbiditiesElement->items)) {
            foreach ($comorbiditiesElement->items as $comorbiditity) {
                $comorbidities[] = $comorbiditity['name'];
            }

            return implode(',', $comorbidities);
        }
    }

    protected function getTargetRefraction($criteria)
    {
        $cataractManagementElement = \OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement::model()->with(array('event'))->find($criteria);

        if ($cataractManagementElement) {
            return $cataractManagementElement['target_postop_refraction'];
        }
    }

    protected function getCataractSurgicalManagement($criteria)
    {
        $cataractSurgicalManagementElement = \OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement::model()->with(array('event'))->find($criteria);
        if ($cataractSurgicalManagementElement) {
            return $cataractSurgicalManagementElement;
        }
    }

    protected function getCataractPrimaryReason($criteria)
    {
        $res = '';
        $cataractSurgicalManagementElement = \OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement::model()->with(array('event', 'leftReasonForSurgery', 'rightReasonForSurgery'))->find($criteria);

        if ($cataractSurgicalManagementElement) {
            foreach (['left', 'right'] as $side) {
                if ($cataractSurgicalManagementElement[$side . 'ReasonForSurgery']) {
                    $reasons[$side] = $cataractSurgicalManagementElement[$side . 'ReasonForSurgery'];
                }
            }
        }

        if ($reasons) {
            foreach ($reasons as $side => $reason) {
                $res .= $side . ' reason: ' . $reason['originalAttributes']['name'] . PHP_EOL;
            }
        }

        return $res;
    }

    public function getFirstEyeOrSecondEye($criteria)
    {
        $cataractManagementElement = \OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement::model()->with(array('event'))->find($criteria);

        if ($cataractManagementElement) {
            return $cataractManagementElement->eye['name'];
        }
    }

    public function getVAReading($criteria, $record)
    {
        $criteria->addInCondition('eye_id', $this->eyesCondition($record));
        $va = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->with(array('event'))->find($criteria);
        $reading = null;
        $sides = array(strtolower($record['eye']));
        if ($sides[0] == 'both') {
            $sides = array('left', 'right');
        }

        if ($va) {
            $res = '';
            foreach ($sides as $side) {
                $reading = $va->getBestReading($side);
                if ($res) {
                    $res .= ' ';
                }
                if ($reading) {
                    $res .= ucfirst($side).': '.$reading->convertTo($reading->value, $reading->unit_id).' ('.$reading->method->name.')';
                } else {
                    $res .= ucfirst($side).': Unknown';
                }
            }

            return $res;
        }

        return 'Unknown';
    }

    public function getVAReadingDate($criteria, $record)
    {
        $criteria->addInCondition('eye_id', $this->eyesCondition($record));
        $va = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->with(array('event'))->find($criteria);

        $sides = array(strtolower($record['eye']));
        if ($sides[0] == 'both') {
            $sides = array('left', 'right');
        }
        $date = 'Unknown';
        if ($va) {
            foreach ($sides as $side) {
                $reading = $va->getBestReading($side);

                if ($reading) {
                    $date = date('j M Y', strtotime($reading->element->event->event_date));
                }
            }
        }

        return $date;
    }

    public function bestPostOpVaValues($criteria, $record)
    {
        $criteria->addInCondition('eye_id', $this->eyesCondition($record));
        $va = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->with(array('event'))->find($criteria);
        $sides = array(strtolower($record['eye']));
        if ($sides[0] == 'both') {
            $sides = array('left', 'right');
        }

        $res = array();
        if ($this->operation_date) {
            $two_weeks = strtotime('+2 weeks', $this->operation_date);
            $six_weeks = strtotime('+6 weeks', $this->operation_date);
            $benchmark_date = $two_weeks;
            if ($va) {
                foreach ($sides as $side) {
                    $readings = $va->getAllReadings($side);
                    $method = '';
                    foreach ($readings as $reading) {
                        if (strtotime($reading->element->event->event_date) >= $two_weeks && strtotime($reading->element->event->event_date) <= $six_weeks) {
                            if (strtotime($reading->element->event->event_date) >= $benchmark_date && $method != $reading->method->name) {
                                $benchmark_date = strtotime($reading->element->event->event_date);
                                $method = $reading->method->name;
                                $res[$reading->method->name.'_'.$side]['side'] = $side;
                                $res[$reading->method->name.'_'.$side]['date'] = date('j M Y', strtotime($reading->element->event->event_date));
                                $res[$reading->method->name.'_'.$side]['method'] = $reading->method->name;
                                $res[$reading->method->name.'_'.$side]['reading'] = $reading->convertTo($reading->value, $reading->unit_id);
                            }
                        }
                    }
                }
            }
        }

        return $res;
    }

    public function getVaReadingSplit($criteria, $record)
    {
        $criteria->addInCondition('eye_id', $this->eyesCondition($record));
        $va = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->with(array('event'))->find($criteria);
        $reading = null;
        $sides = array(strtolower($record['eye']));
        if ($sides[0] == 'both') {
            $sides = array('left', 'right');
        }

        $res = array();
        if ($va) {
            $res = array();
            foreach ($sides as $side) {
                $readings = $va->getAllReadings($side);
                if ($readings) {
                    foreach ($readings as $reading) {
                        $res[] = array(
                            'side' => ucfirst($side),
                            'va_reading' => $reading->convertTo($reading->value, $reading->unit_id),
                            'method' => $reading->method->name);
                    }
                } else {
                    $res[] = array('side' => ucfirst($side), 'va_reading' => '', 'method' => '');
                }
            }
        } else {
            $res[] = array('side' => '', 'va_reading' => '', 'method' => '');
        }

        return $res;
    }

    public function getRefractionReading($criteria, $record)
    {
        $criteria->addInCondition('eye_id', $this->eyesCondition($record));
        $refraction = \OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction::model()->with('event')->find($criteria);
        if ($refraction) {
            return $refraction->getPriorityReadingCombined(strtolower($record['eye']));
        } else {
            return 'Unknown';
        }
    }

    public function getRefractionReadingSplit($criteria, $record)
    {
        $criteria->addInCondition('eye_id', $this->eyesCondition($record));
        $refraction = \OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction::model()->with('event')->find($criteria);
        $refraction_values = array();
        if ($refraction) {
            $refraction_values = $refraction->getPriorityReadingDataAttributes(strtolower($record['eye']));
        }

        return $refraction_values;
    }

    public function getPostOpRefractionSplit($criteria, $record)
    {
        $criteria->addInCondition('eye_id', $this->eyesCondition($record));
        $refraction = \OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction::model()->with('event')->find($criteria);
        $refraction_values = array();

        if ($refraction) {
            if ($this->operation_date) {
                $two_weeks = strtotime('+2 weeks', $this->operation_date);
                $six_weeks = strtotime('+6 weeks', $this->operation_date);

                $benchmark_date = $two_weeks;

                if (strtotime($refraction->event->event_date) >= $two_weeks && strtotime($refraction->event->event_date) <= $six_weeks) {
                    if (strtotime($refraction->event->event_date) >= $benchmark_date) {
                        $refraction_values = $refraction->getPriorityReadingDataAttributes(strtolower($record['eye']));
                        $refraction_values['date'] = date('j M Y', strtotime($refraction->event->event_date));
                    }
                }
            }
        }

        return $refraction_values;
    }

    protected function appendOpNoteValues(&$record, $event_id, $anaesthetic_type, $anaesthetic_delivery, $anaesthetic_comments, $anaesthetic_complications, $cataract_report, $incision_site, $cataract_complication_notes, $cataract_predicted_refraction, $cataract_iol_type, $cataract_iol_power, $tamponade_used, $surgeon, $surgeon_role, $assistant, $assistant_role, $supervising_surgeon, $supervising_surgeon_role, $opnote_comments)
    {
        $anaesthetic = Element_OphTrOperationnote_Anaesthetic::model()->find('event_id = :event_id', array(':event_id' => $event_id));

        if ($anaesthetic_type && $anaesthetic) {
            $record['anaesthetic_type'] = '';
            foreach ($anaesthetic->anaesthetic_type as $anaesthetic_type) {
                $record['anaesthetic_type'] .= !$record['anaesthetic_type'] ? '' : ', ';
                $record['anaesthetic_type'] .= $anaesthetic_type->name;
            }
        }


        if ($anaesthetic_delivery && $anaesthetic) {
            $record['anaesthetic_delivery'] = '';
            foreach ($anaesthetic->anaesthetic_delivery as $anaesthetic_delivery) {
                $record['anaesthetic_delivery'] .= !$record['anaesthetic_delivery'] ? '' : ', ';
                $record['anaesthetic_delivery'] .= $anaesthetic_delivery->name;
            }
        }

        if ($anaesthetic_comments) {
            $record['anaesthetic_comments'] = $anaesthetic['anaesthetic_comment'];
        }

        if ($anaesthetic_complications) {
            $complications = array();
            if (isset($anaesthetic->anaesthetic_complications)) {
                foreach ($anaesthetic->anaesthetic_complications as $complication) {
                    $complications[] = $complication['name'];
                }
                $record['anaesthetic_complications'] = implode(',', $complications);
            }
        }

        if ($cataract_report) {
            foreach (array('cataract_report', 'cataract_predicted_refraction', 'cataract_iol_type', 'cataract_iol_power') as $k) {
                $record[$k] = '';
            }
            if ($cataract_element = Element_OphTrOperationnote_Cataract::model()->find('event_id = :event_id', array(':event_id' => $event_id))) {
                $record['cataract_report'] = trim(preg_replace('/\s\s+/', ' ', $cataract_element['report']));
                $record['cataract_predicted_refraction'] = $cataract_element->predicted_refraction;
                $record['cataract_iol_type'] = $cataract_element->iol_type ? $cataract_element->iol_type->display_name : 'None';
                $record['cataract_iol_power'] = $cataract_element->iol_power;
            }
        }

        if ($incision_site) {
            $record['incision_site'] = 'Unknown';
            $record['length_of_incision'] = 'Unknown';
            $record['meridian'] = 'Unknown';
            $record['incision_type'] = 'Unknown';
            $record['iol_position'] = 'Unknown';

            $cataract_operation_details_element = Element_OphTrOperationnote_Cataract::model()->find('event_id = :event_id', array(':event_id' => $event_id));
            if ($cataract_operation_details_element) {
                $incisionSite = OphTrOperationnote_IncisionSite::model()->find('id = :id', array(':id' => $cataract_operation_details_element['incision_site_id']));
                if ($incisionSite) {
                    $record['incision_site'] = $incisionSite->name;
                }

                if ($cataract_operation_details_element['length']) {
                    $record['length_of_incision'] = $cataract_operation_details_element['length'];
                }

                if ($cataract_operation_details_element['meridian']) {
                    $record['meridian'] = $cataract_operation_details_element['meridian'];
                }

                $incision = OphTrOperationnote_IncisionType::model()->find('id = :id', array(':id' => $cataract_operation_details_element['incision_type_id']));
                if ($incision) {
                    $record['incision_type'] = $incision->name;
                }

                $iolPosition = OphTrOperationnote_IOLPosition::model()->find('id = :id', array(':id' => $cataract_operation_details_element['iol_position_id']));
                if ($iolPosition) {
                    $record['iol_position'] = $iolPosition->name;
                }
            }
        }

        if ($cataract_complication_notes) {
            $record['Cataract Complication Notes'] = 'Unknown';
            if ($cataract_complication_notes_element = Element_OphTrOperationnote_Cataract::model()->find('event_id = :event_id', array(':event_id' => $event_id))) {
                if ($cataract_complication_notes_element['complication_notes']) {
                    $record['Cataract Complication Notes'] = $cataract_complication_notes_element['complication_notes'];
                }
            }
        }

        if ($tamponade_used) {
            if ($tamponade_element = Element_OphTrOperationnote_Tamponade::model()->find('event_id = :event_id', array(':event_id' => $event_id))) {
                $record['tamponade_used'] = $tamponade_element->gas_type->name;
            } else {
                $record['tamponade_used'] = 'None';
            }
        }

        if ($surgeon || $surgeon_role || $assistant || $assistant_role || $supervising_surgeon || $supervising_surgeon_role) {
            $surgeon_element = Element_OphTrOperationnote_Surgeon::model()->findByAttributes(array('event_id' => $event_id));

            foreach (array('surgeon', 'assistant', 'supervising_surgeon') as $surgeon_type) {
                $role = $surgeon_type.'_role';
                if (${$surgeon_type} || ${$role}) {
                    $surgeon = $surgeon_element->{$surgeon_type};
                    if (${$surgeon_type}) {
                        $record[$surgeon_type] = $surgeon ? $surgeon->getFullName() : 'None';
                    }
                    if (${$role}) {
                        $record["{$surgeon_type}_role"] = $surgeon ? $surgeon->role : 'None';
                    }
                }
            }
        }

        if ($this->opnote_comments) {
            $comments = Element_OphTrOperationnote_Comments::model()->find('event_id = :event_id', array(':event_id' => $event_id));
            $record['opnote_comments'] = trim(preg_replace('/\s\s+/', ' ', $comments['comments']));
        }
    }

    public function getColumns()
    {
        $return = array(
            'Operation date',
            $this->getPatientIdentifierPrompt(),
            Patient::model()->getAttributeLabel('first_name'),
            Patient::model()->getAttributeLabel('last_name'),
            Patient::model()->getAttributeLabel('gender'),
            Patient::model()->getAttributeLabel('dob'),
            'Eye',
            'Procedures',
            'Complications',
        );

        if ($this->surgeon_id) {
            $return[] = 'Role';
        }

        foreach (array(
                     'patient_oph_diagnoses',
                     'booking_diagnosis',
                     'theatre',
                     'bookingcomments',
                     'surgerydate',
                     'anaesthetic_type',
                     'anaesthetic_delivery',
                     'anaesthetic_comments',
                     'anaesthetic_complications',
                     'cataract_report' => array(
                         'cataract_predicted_refraction',
                         'cataract_iol_type',
                         'cataract_iol_power',
                     ),
                     'incision_site' => array(
                         'length_of_incision',
                         'meridian',
                         'incision_type',
                         'iol_position',
                     ),
                     'cataract_complication_notes',
                     'tamponade_used',
                     'surgeon',
                     'surgeon_role',
                     'assistant',
                     'assistant_role',
                     'supervising_surgeon',
                     'supervising_surgeon_role',
                     'opnote_comments',
                     'comorbidities',
                     'first_eye',
                     'target_refraction',
                 ) as $key => $value) {
            if (is_int($key)) {
                if ($this->$value) {
                    $return[] = $this->getAttributeLabel($value);
                }
            } else {
                if ($this->$key) {
                    $return[] = $this->getAttributeLabel($key);
                    foreach ($value as $key2) {
                        $return[] = $this->getAttributeLabel($key2);
                    }
                }
            }
        }
        if ($this->refraction_values) {
            $return[] = 'Pre-op refraction';
            $return[] = 'Pre-op sphere';
            $return[] = 'Pre-op cylinder';
            $return[] = 'Pre-op axis';
            $return[] = 'Pre-op type';
            $return[] = 'Pre-op Spherical equivalent';
            $return[] = 'Post-op Refraction (2-6 weeks) date';
            $return[] = 'Post-op 2-6 weeks sphere';
            $return[] = 'Post-op 2-6 weeks cylinder';
            $return[] = 'Post-op 2-6 weeks axis';
            $return[] = 'Post-op 2-6 weeks type';
            $return[] = 'Post-op 2-6 weeks Spherical equivalent';
            $return[] = 'Most recent post-op refraction';
            $return[] = 'Post-op sphere';
            $return[] = 'Post-op cylinder';
            $return[] = 'Post-op axis';
            $return[] = 'Post-op type';
            $return[] = 'Post-op Spherical equivalent';
        }
        if ($this->cataract_surgical_management) {
            $return[] = 'Post Op Refractive Target Discussed With Patient';
            $return[] = 'Previous Refractive Surgery';
            $return[] = 'Vitrectomised Eye';
            $return[] = 'Primary reason for cataract surgery';
        }

        if ($this->va_values) {
            $return[] = 'Pre-op VA Date';
            $return[] = 'Pre-op VA';
            $return[] = 'Pre-op VA Glasses';
            $return[] = 'Pre-op VA Unaided';
            $return[] = 'Pre-op VA Pinhole';
            $return[] = 'Post-op VA (2-6 weeks) date';
            $return[] = '2-6 Week Post-op VA Glasses';
            $return[] = '2-6 Week Post-op VA Unaided';
            $return[] = '2-6 Week Post-op VA Pinhole';
            $return[] = 'Most recent post-op VA';
            $return[] = 'Most recent post-op Glasses';
            $return[] = 'Most recent post-op Unaided';
            $return[] = 'Most recent post-op Pinhole';
        }

        $return[] = $this->getAttributeLabel('all_ids');

        return $return;
    }

    public function description()
    {
        $description = 'Operations';

        if ($this->surgeon_id) {
            $description .= ' by '.User::model()->find($this->surgeon_id)->fullName;
        }

        $description .= ' between '.date('j M Y', strtotime($this->date_from)).' and '.date('j M Y', strtotime($this->date_to));

        if (!empty($this->Procedures_procs)) {
            $description .= "\nwith procedures: ";

            foreach ($this->Procedures_procs as $i => $proc_id) {
                if ($i) {
                    $description .= ', ';
                }
                $description .= Procedure::model()->findByPk($proc_id)->term;
            }
        }

        if (!empty($this->complications)) {
            $description .= "\nwith cataract complications: ";

            foreach ($this->complications as $i => $complication_id) {
                if ($i) {
                    $description .= ', ';
                }
                $description .= OphTrOperationnote_CataractComplications::model()->findByPk($complication_id)->name;
            }
        }

        return $description;
    }

    /**
     * Output the report in CSV format.
     *
     * @return string
     */
    public function toCSV()
    {
        $output = $this->description()."\n\n ";
        $output .= implode(',', $this->getColumns())."\n";

        return $output.$this->array2Csv($this->operations);
    }
}
