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
class OphTrIntravitrealinjection_ReportInjections extends BaseReport
{
    public $date_from;
    public $date_to;
    public $given_by_id;
    public $summary;
    public $drug_id;
    public $pre_antisept_drug_id;
    public $pre_va;
    public $post_va;
    public $injections;

    private $patient_id = null;
    protected $_drug_cache = array();
    protected $_examination_event_type_id;
    protected $_application_event_type_id;
    protected $_current_patient_id;
    protected $_patient_vas;

    public function attributeNames()
    {
        return array(
            'date_from',
            'date_to',
            'given_by_id',
            'drug_id',
            'pre_antisept_drug_id',
            'summary',
            'pre_va',
            'post_va',
        );
    }

    public function attributeLabels()
    {
        return array(
            'date_from' => 'Date from',
            'date_to' => 'Date to',
            'given_by_id' => 'Given by',
            'summary' => 'Summarise patient data',
            'pre_va' => 'Pre injection VA',
            'post_va' => 'Post injection VA',
            'drug_id' => 'Drug',
            'pre_antisept_drug_id' => 'Pre-injection Antiseptics',
            'all_ids' => 'Patient IDs'
        );
    }

    public function rules()
    {
        return array(
            array('date_from, date_to, given_by_id, summary, pre_va, post_va, drug_id, pre_antisept_drug_id, institution_id', 'safe'),
            array('date_from, date_to, summary, pre_va, post_va', 'required'),
        );
    }

    public function run()
    {
        $user_id = Yii::app()->user->id;
        $this->setInstitutionAndSite($user_id);

        if (!$this->date_from) {
            $this->date_from = date('Y-m-d', strtotime('-1 year'));
        } else {
            $this->date_from = date('Y-m-d', strtotime($this->date_from));
        }

        if (!$this->date_to) {
            $this->date_to = date('Y-m-d');
        } else {
            $this->date_to = date('Y-m-d', strtotime($this->date_to));
        }

        //If user does NOT have the RBAC role 'Report' then select the current user
        if (!Yii::app()->getAuthManager()->checkAccess('Report', $user_id)) {
            $this->given_by_id = $user_id;
        }

        if ($this->given_by_id) {
            if (!$user = User::model()->findByPk($this->given_by_id)) {
                throw new Exception('User not found: ' . $this->given_by_id);
            }
        }

        if ($this->drug_id) {
            if (!$drug = OphTrIntravitrealinjection_Treatment_Drug::model()->findByPk($this->drug_id)) {
                throw new Exception('Drug not found: ' . $this->drug_id);
            }
        }

        if ($this->pre_antisept_drug_id) {
            if (!$pre_antisept_drug = OphTrIntravitrealinjection_AntiSepticDrug::model()->findByPk($this->pre_antisept_drug_id)) {
                throw new Exception('Drug not found: ' . $this->pre_antisept_drug_id);
            }
        }

        if ($this->summary) {
            $this->injections = $this->getSummaryInjections(
                $this->date_from,
                $this->date_to,
                @$user,
                @$drug,
                @$pre_antisept_drug
            );
            $this->view = '_summary_injections';
        } else {
            $this->injections = $this->getInjections(
                $this->date_from,
                $this->date_to,
                @$user,
                @$drug,
                @$pre_antisept_drug
            );
            $this->view = '_injections';
        }
    }

    protected function extractSummaryData($patient_data)
    {
        $records = array();
        foreach (array('left', 'right') as $side) {
            if (@$patient_data[$side]) {
                foreach (array_keys($patient_data[$side]) as $drug) {
                    foreach (array_keys($patient_data[$side][$drug]) as $site) {
                        $records[] = array(
                            'patient_identifier' => $patient_data['patient_identifier'],
                            'patient_firstname' => $patient_data['patient_firstname'],
                            'patient_surname' => $patient_data['patient_surname'],
                            'patient_gender' => $patient_data['patient_gender'],
                            'patient_dob' => $patient_data['patient_dob'],
                            'eye' => $side,
                            'drug' => $drug,
                            'site' => $site,
                            'first_injection_date' => $patient_data[$side][$drug][$site]['first_injection_date'],
                            'last_injection_date' => $patient_data[$side][$drug][$site]['last_injection_date'],
                            'injection_number' => $patient_data[$side][$drug][$site]['injection_number'],
                            'all_ids' => $patient_data['all_ids'],
                        );
                    }
                }
            }
        }

        return $records;
    }

    protected function getInjections($date_from, $date_to, $given_by_user, $drug, $pre_antisept_drug)
    {
        $patient_data = array();
        $where = '';
        $command = Yii::app()->db->createCommand()
            ->select(
                'p.id as patient_id, treat.left_drug_id, treat.right_drug_id, treat.left_number, treat.right_number, e.id,
						e.event_date, c.first_name, c.last_name, e.created_date, p.gender, p.dob, eye.name AS eye, site.name as site_name'
            )
            ->from('et_ophtrintravitinjection_treatment treat')
            ->join('event e', 'e.id = treat.event_id')
            ->join('episode ep', 'e.episode_id = ep.id')
            ->join('patient p', 'ep.patient_id = p.id')
            ->join('contact c', 'p.contact_id = c.id')
            ->join('eye', 'eye.id = treat.eye_id')
            ->join('et_ophtrintravitinjection_site insite', 'insite.event_id = treat.event_id')
            ->leftJoin('site', 'insite.site_id = site.id')
            ->order('p.id, e.event_date asc');
        // for debug
        if ($this->patient_id) {
            $where = 'ep.patient_id = :pat_id and e.deleted = 0 and ep.deleted = 0 and e.event_date >= :from_date and e.event_date < (:to_date + interval 1 day)';
            $params = array(':from_date' => $date_from, ':to_date' => $date_to, ':pat_id' => $this->patient_id);
        } else {
            $where = 'e.deleted = 0 and ep.deleted = 0 and e.event_date >= :from_date and e.event_date < (:to_date + interval 1 day)';
            $params = array(':from_date' => $date_from, ':to_date' => $date_to);
        }

        if ($given_by_user) {
            $where .= ' and (treat.right_injection_given_by_id = :user_id or treat.left_injection_given_by_id = :user_id)';
            $params[':user_id'] = $given_by_user->id;
        }

        if ($this->institution_id) {
            $where .= ' and (e.institution_id = :institution_id)';
            $params[':institution_id'] = $this->institution_id;
        }

        if ($drug) {
            $where .= ' and (treat.left_drug_id = :drug_id or treat.right_drug_id = :drug_id)';
            $params[':drug_id'] = $drug->id;
        }

        if ($pre_antisept_drug) {
            $where .= ' and (treat.left_pre_antisept_drug_id = :pre_antisept_drug_id or treat.right_pre_antisept_drug_id = :pre_antisept_drug_id)';
            $params[':pre_antisept_drug_id'] = $pre_antisept_drug->id;
        }

        $command->where($where);

        $results = array();
        foreach ($command->queryAll(true, $params) as $row) {
            if (@$patient_data['id'] != $row['patient_id']) {
                if (@$patient_data['id']) {
                    foreach ($this->extractSummaryData($patient_data) as $record) {
                        $results[] = $record;
                    }
                }

                $patient_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $row['patient_id'], $this->user_institution_id, $this->user_selected_site_id));
                $patient_identifiers = PatientIdentifierHelper::getAllPatientIdentifiersForReports($row['patient_id']);

                $patient_data = array(
                    'id' => $row['patient_id'],
                    'patient_identifier' => $patient_identifier_value,
                    'all_ids' => $patient_identifiers,
                    'patient_firstname' => $row['first_name'],
                    'patient_surname' => $row['last_name'],
                    'patient_gender' => $row['gender'],
                    'patient_dob' => date('j M Y', strtotime($row['dob'])),
                );
            }
            if (!$site = @$row['site_name']) {
                $site = 'Unknown';
            }
            foreach (array('left', 'right') as $side) {
                $dt = date('j M Y', strtotime($row['event_date']));
                if ($drug = $this->getDrugById($row[$side . '_drug_id'])) {
                    $patient_data[$side][$drug->name][$site]['last_injection_date'] = $dt;
                    $patient_data[$side][$drug->name][$site]['injection_number'] = $row[$side . '_number'];
                    if (!isset($patient_data[$side][$drug->name][$site]['first_injection_date'])) {
                        $patient_data[$side][$drug->name][$site]['first_injection_date'] = $dt;
                    }
                }
            }
        }
        foreach ($this->extractSummaryData($patient_data) as $record) {
            $results[] = $record;
        }

        return $results;
    }

    protected function getSummaryInjections($date_from, $date_to, $given_by_user, $drug, $pre_antisept_drug)
    {
        $where = 'e.deleted = 0 and ep.deleted = 0 and e.event_date >= :from_date and e.event_date < (:to_date + interval 1 day)';

        $command = Yii::app()->db->createCommand()
            ->select(
                'p.id as patient_id, treat.left_drug_id, treat.right_drug_id, treat.left_number, treat.right_number, e.id,
                e.event_date, c.first_name, c.last_name, p.gender, p.dob, eye.name AS eye, site.name as site_name,
                treat.left_injection_given_by_id, treat.right_injection_given_by_id,
                treat.left_pre_antisept_drug_id, treat.right_pre_antisept_drug_id, anteriorseg.left_lens_status_id, anteriorseg.right_lens_status_id'
            )
            ->from('et_ophtrintravitinjection_treatment treat')
            ->join('event e', 'e.id = treat.event_id')
            ->join('episode ep', 'e.episode_id = ep.id')
            ->join('patient p', 'ep.patient_id = p.id')
            ->join('contact c', 'p.contact_id = c.id')
            ->join('eye', 'eye.id = treat.eye_id')
            ->join('et_ophtrintravitinjection_site insite', 'insite.event_id = treat.event_id')
            ->join('et_ophtrintravitinjection_anteriorseg anteriorseg', 'anteriorseg.event_id = treat.event_id')
            ->join('site', 'insite.site_id = site.id')
            ->order('p.id, e.event_date asc');
        $params = array(':from_date' => $date_from, ':to_date' => $date_to);

        if ($given_by_user) {
            $where .= ' and (treat.right_injection_given_by_id = :user_id or treat.left_injection_given_by_id = :user_id)';
            $params[':user_id'] = $given_by_user->id;
        }

        if ($drug) {
            $where .= ' and (treat.left_drug_id = :drug_id or treat.right_drug_id = :drug_id)';
            $params[':drug_id'] = $drug->id;
        }

        if ($pre_antisept_drug) {
            $where .= ' and (treat.left_pre_antisept_drug_id = :pre_antisept_drug_id or treat.right_pre_antisept_drug_id = :pre_antisept_drug_id)';
            $params[':pre_antisept_drug_id'] = $pre_antisept_drug->id;
        }

        if ($this->institution_id) {
            $where .= ' and (e.institution_id = :institution_id)';
            $params[':institution_id'] = $this->institution_id;
        }

        $command->where($where);

        $results = array();
        foreach ($command->queryAll(true, $params) as $row) {
            $diagnosisData = $this->getDiagnosisData($row['patient_id'], $date_to . ' 23:59:59');
            $patient_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $row['patient_id'], $this->user_institution_id, $this->user_selected_site_id));
            $patient_identifiers = PatientIdentifierHelper::getAllPatientIdentifiersForReports($row['patient_id']);

            $record = array(
                'injection_date' => date('j M Y', strtotime($row['event_date'])),
                'patient_identifier' => $patient_identifier_value,
                'patient_firstname' => $row['first_name'],
                'patient_surname' => $row['last_name'],
                'patient_gender' => $row['gender'],
                'patient_dob' => date('j M Y', strtotime($row['dob'])),
                'eye' => $row['eye'],
                'site_name' => $row['site_name'],
                'left_drug' => $this->getDrugString($row['left_drug_id']),
                'left_injection_number' => $row['left_number'],
                'right_drug' => $this->getDrugString($row['right_drug_id']),
                'right_injection_number' => $row['right_number'],
                'pre_antisept_drug_left' => $this->getPreAntiseptDrugString($row['left_pre_antisept_drug_id']),
                'pre_antisept_drug_right' => $this->getPreAntiseptDrugString($row['right_pre_antisept_drug_id']),
                'given_by_left' => $this->getGivenByName($row['left_injection_given_by_id']),
                'given_by_right' => $this->getGivenByName($row['right_injection_given_by_id']),
                'lens_status_left' => $this->getLensStatus($row['left_lens_status_id']),
                'lens_status_right' => $this->getLensStatus($row['right_lens_status_id']),
                'diagnosis_left' => $this->getDiagnosisName($diagnosisData['left_diagnosis_id']),
                'diagnosis_right' => $this->getDiagnosisName($diagnosisData['right_diagnosis_id']),
                'all_ids' => $patient_identifiers,
            );

            $this->appendExaminationValues($record, $row['patient_id'], $row['event_date']);

            $results[] = $record;
        }

        return $results;
    }

    public function description()
    {
        if ($this->summary) {
            $description = 'Summary of injections';
        } else {
            $description = 'Injections';
        }

        if ($this->given_by_id) {
            $description .= ' given by ' . User::model()->findByPk($this->given_by_id)->fullName;
        }

        $description .= ' between ' . date('j M Y', strtotime($this->date_from)) . ' and ' . date(
            'j M Y',
            strtotime($this->date_to)
        );

        if ($this->pre_va && $this->post_va) {
            $description .= ' with pre-injection and post-injection VA';
        } elseif ($this->pre_va) {
            $description .= ' with pre-injection VA';
        } elseif ($this->post_va) {
            $description .= ' with post-injection VA';
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
        $output = $this->description() . "\n\n";

        if (!$this->summary) {
            $output .=
                $this->getPatientIdentifierPrompt() . ',' .
                Patient::model()->getAttributeLabel('first_name') . ',' .
                Patient::model()->getAttributeLabel('last_name') . ',' .
                Patient::model()->getAttributeLabel('gender') . ',' .
                Patient::model()->getAttributeLabel('dob') . ',Eye,Drug,Site,First injection date,Last injection date,Injection no,Patient IDs';
        } else { // SUMMARY
            $output .=
                'Date,' .
                $this->getPatientIdentifierPrompt() . ',' .
                Patient::model()->getAttributeLabel('first_name') . ',' .
                Patient::model()->getAttributeLabel('last_name') . ',' .
                Patient::model()->getAttributeLabel('gender') . ',' .
                Patient::model()->getAttributeLabel('dob') . ',Eye,Site,Left drug,Left injection no,Right drug,Right injection no,Left Pre-injection Antiseptics,Right Pre-injection Antiseptics,Left Injection given by,Right Injection given by,Left Lens Status,Right Lens Status,Left Diagnosis,Right Diagnosis,Patient IDs';

            if ($this->pre_va) {
                $output .= ',Left pre-injection VA,Right pre-injection VA';
            }
            if ($this->post_va) {
                $output .= ',Left post-injection VA,Right post-injection VA';
            }
        }

        $output .= "\n";
        return $output . $this->array2Csv($this->injections);
    }

    protected function getDiagnosisDataFromEvent($patient_id, $close_to_date, $event_type_id, $model)
    {
        $command = Yii::app()->db->createCommand()
            ->select('e.id')
            ->from('event e')
            ->join('episode ep', 'e.episode_id = ep.id')
            ->where(
                'e.deleted = 0 and ep.deleted = 0 and ep.patient_id = :patient_id and
                e.event_type_id = :etype_id and e.event_date <= :close_date',
                array(':patient_id' => $patient_id, ':etype_id' => $event_type_id, ':close_date' => $close_to_date)
            )->order('event_date desc')->limit(1);

        $eventData = $command->queryRow();

        $left_diagnosis_id = 0;
        $right_diagnosis_id = 0;

        if ($eventData) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('event_id = ' . $eventData['id']);
            $injectionManagementData = $model::model()->find($criteria);

            //var_dump($injectionManagementData);

            if ($injectionManagementData) {
                if (isset($injectionManagementData->left_diagnosis2_id) && $injectionManagementData->left_diagnosis2_id > 0) {
                    $left_diagnosis_id = $injectionManagementData->left_diagnosis2_id;
                } elseif (isset($injectionManagementData->left_diagnosis1_id) && $injectionManagementData->left_diagnosis1_id > 0) {
                    $left_diagnosis_id = $injectionManagementData->left_diagnosis1_id;
                }
                if (isset($injectionManagementData->right_diagnosis2_id) && $injectionManagementData->right_diagnosis2_id > 0) {
                    $right_diagnosis_id = $injectionManagementData->right_diagnosis2_id;
                } elseif (isset($injectionManagementData->right_diagnosis1_id) && $injectionManagementData->right_diagnosis1_id > 0) {
                    $right_diagnosis_id = $injectionManagementData->right_diagnosis1_id;
                }
            }
        }

        //var_dump('Patient: '.$patient_id.' LEFT: '.$left_diagnosis_id." RIGHT: ".$right_diagnosis_id." Command: ".$command->getText()." :: ".print_r($command->params));
        return array('left_diagnosis_id' => $left_diagnosis_id, 'right_diagnosis_id' => $right_diagnosis_id);
    }

    /**
     * a) From the injection management element under Examination event saved before the injection event - usually on the same day
     *    b) If no injection management saved then this can be obtained from the application event If there is an application event saved before the injection started for the patient
     *    c) if there is no application then diagnoses for the episode.
     * @param $patient_id
     * @param $close_to_date
     * @return array
     */
    protected function getDiagnosisData($patient_id, $close_to_date)
    {
        // check for examination data
        // search for the closest examination event first

        $diagnosisData = $this->getDiagnosisDataFromEvent(
            $patient_id,
            $close_to_date,
            $this->getExaminationEventTypeId(),
            'OEModule\OphCiExamination\models\Element_OphCiExamination_InjectionManagementComplex'
        );

        if (!$diagnosisData['left_diagnosis_id'] && !$diagnosisData['right_diagnosis_id']) {
            $diagnosisData = $this->getDiagnosisDataFromEvent(
                $patient_id,
                $close_to_date,
                $this->getApplicationEventTypeID(),
                'Element_OphCoTherapyapplication_Therapydiagnosis'
            );
        }

        return $diagnosisData;
    }

    /**
     * simple cache for drug objects.
     *
     * @param $drug_id
     *
     * @return OphTrIntravitrealinjection_Treatment_Drug|null
     */
    protected function getDrugById($drug_id)
    {
        if (!@$this->_drug_cache[$drug_id]) {
            $this->_drug_cache[$drug_id] = OphTrIntravitrealinjection_Treatment_Drug::model()->findByPk($drug_id);
        }

        return $this->_drug_cache[$drug_id];
    }

    protected function getPreAntiseptDrugString($drug_id)
    {
        if (!$drug_id) {
            return 'N/A';
        }
        if ($drug = OphTrIntravitrealinjection_AntiSepticDrug::model()->findByPk($drug_id)) {
            return $drug->name;
        } else {
            return 'UNKNOWN';
        }
    }

    protected function getGivenByName($user_id)
    {
        if (!$user_id) {
            return 'N/A';
        }
        if ($user = User::model()->findByPk($user_id)) {
            return $user->first_name . ' ' . $user->last_name;
        } else {
            return 'UNKNOWN';
        }
    }

    protected function getLensStatus($lens_status_id)
    {
        if (!$lens_status_id) {
            return 'N/A';
        }
        if ($lens_status = OphTrIntravitrealinjection_LensStatus::model()->findByPk($lens_status_id)) {
            return $lens_status->name;
        } else {
            return 'UNKNOWN';
        }
    }

    protected function getDiagnosisName($disorder_id)
    {
        if (!$disorder_id) {
            return 'N/A';
        }
        if ($disorder = Disorder::model()->findByPk($disorder_id)) {
            return $disorder->term;
        } else {
            return 'UNKNOWN';
        }
    }

    /**
     * Return the printable string for the drug.
     *
     * @param $drug_id
     *
     * @return string
     */
    protected function getDrugString($drug_id)
    {
        if (!$drug_id) {
            return 'N/A';
        }
        if ($drug = $this->getDrugById($drug_id)) {
            return $drug->name;
        } else {
            return 'UNKNOWN';
        }
    }

    protected function appendExaminationValues(&$record, $patient_id, $event_date)
    {
        if ($this->pre_va || $this->post_va) {
            foreach (
                array(
                         'left_preinjection_va',
                         'right_preinjection_va',
                         'left_postinjection_va',
                         'right_postinjection_va',
                     ) as $k
            ) {
                $record[$k] = 'N/A';
            }
            $vas = $this->getPatientVAElements($patient_id);
            $before = null;
            $after = null;
            foreach ($vas as $va) {
                if ($va->event->event_date < $event_date) {
                    $before = $va;
                } elseif ($va->event->event_date > $event_date) {
                    $after = $va;
                    break;
                }
            }
            if ($this->pre_va) {
                if ($before) {
                    $record['left_preinjection_va'] = $this->getBestVaFromReading('left', $before);
                    $record['right_preinjection_va'] = $this->getBestVaFromReading('right', $before);
                } else {
                    $record['left_preinjection_va'] = 'N/A';
                    $record['right_preinjection_va'] = 'N/A';
                }
            }
            if ($this->post_va) {
                if ($after) {
                    $record['left_postinjection_va'] = $this->getBestVaFromReading('left', $after);
                    $record['right_postinjection_va'] = $this->getBestVaFromReading('right', $after);
                } else {
                    $record['left_postinjection_va'] = 'N/A';
                    $record['right_postinjection_va'] = 'N/A';
                }
            }
        }
    }

    /**
     * in order to suck up too much memory for larger reports, when this method receives a call for a new patient, it ditches the cache
     * it has of the previous patient.
     *
     * @param $patient_id
     *
     * @return Element_OphCiExamination_VisualAcuity[]
     */
    protected function getPatientVAElements($patient_id)
    {
        if ($patient_id != $this->_current_patient_id) {
            $this->_current_patient_id = $patient_id;
            $command = Yii::app()->db->createCommand()
                ->select('e.id')
                ->from('event e')
                ->join('episode ep', 'e.episode_id = ep.id')
                ->where(
                    'e.deleted = 0 and ep.deleted = 0 and ep.patient_id = :patient_id and e.event_type_id = :etype_id',
                    array(':patient_id' => $patient_id, ':etype_id' => $this->getExaminationEventTypeId())
                );
            $event_ids = array();
            foreach ($command->queryAll() as $res) {
                $event_ids[] = $res['id'];
            }
            $criteria = new CDbCriteria();
            $criteria->addInCondition('event_id', $event_ids);
            $criteria->order = 'event_date asc';
            $this->_patient_vas = OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()
                ->with('right_readings', 'left_readings', 'event')
                ->findAll($criteria);
        }

        return $this->_patient_vas;
    }

    /**
     * Simple wrapper function for getting a string representation of the best VA reading for a side from the given element.
     *
     * @param $side
     * @param OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity $va
     *
     * @return string
     */
    protected function getBestVaFromReading(
        $side,
        OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity $va
    ) {
        if ($reading = $va->getBestReading($side)) {
            return $reading->convertTo($reading->value, $reading->unit_id) . ' (' . $reading->method->name . ')';
        }

        return 'N/A';
    }

    protected function getExaminationEventTypeId()
    {
        if (!$this->_examination_event_type_id) {
            $this->_examination_event_type_id = EventType::model()->findByAttributes(array('class_name' => 'OphCiExamination'))->id;
        }

        return $this->_examination_event_type_id;
    }

    protected function getApplicationEventTypeID()
    {
        if (!$this->_application_event_type_id) {
            $this->_application_event_type_id = EventType::model()->findByAttributes(array('class_name' => 'OphCoTherapyapplication'))->id;
        }

        return $this->_application_event_type_id;
    }
}
