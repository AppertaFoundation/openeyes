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
class ReportController extends BaseReportController
{
    public $renderPatientPanel = false;

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('applications', 'pendingApplications'),
                'expression' => array('ReportController', 'checkSurgonOrRole'),
            ),
        );
    }

    protected function array2Csv(array $data)
    {
        if (count($data) == 0) {
            return;
        }
        ob_start();
        $df = fopen('php://output', 'w');
        fputcsv($df, array_keys(reset($data)));
        foreach ($data as $row) {
            fputcsv($df, $row);
        }
        fclose($df);

        return ob_get_clean();
    }

    protected function sendCsvHeaders($filename)
    {
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename=$filename");
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    public function actionApplications()
    {
        $date_from = date(Helper::NHS_DATE_FORMAT, strtotime('-1 year'));
        $date_to = date(Helper::NHS_DATE_FORMAT);
        if (isset($_GET['yt0'])) {
            $firm = null;

            if (@$_GET['firm_id'] && (int) $_GET['firm_id']) {
                $firm_id = (int) $_GET['firm_id'];
                if (!$firm = Firm::model()->findByPk($firm_id)) {
                    throw new CException("Unknown firm $firm_id");
                }

                if (!Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id)) {
                    //if the user has no Report role than he/she must be a consultant
                    if ($firm->consultant_id !== Yii::app()->user->id) {
                        throw new CException("Not authorised: Only for consultant");
                    }
                }
            }
            if (@$_GET['date_from'] && date('Y-m-d', strtotime($_GET['date_from']))) {
                $date_from = date('Y-m-d', strtotime($_GET['date_from']));
            }
            if (@$_GET['date_to'] && date('Y-m-d', strtotime($_GET['date_to']))) {
                $date_to = date('Y-m-d', strtotime($_GET['date_to']));
            }

            $institution_id = Yii::app()->request->getParam('institution_id', null);

            $results = $this->getApplications($date_from, $date_to, $firm, $institution_id);

            $filename = 'therapyapplication_report_' . date('YmdHis') . '.csv';
            $this->sendCsvHeaders($filename);

            echo $this->array2Csv($results);

            $get = array('report-name' => 'Therapy applications') + $_GET;
            Audit::add('Reports', 'download', "<pre>" . print_r($get, true) . "</pre>");
        } else {
            $subspecialty = Subspecialty::model()->find('ref_spec=:ref_spec', array(':ref_spec' => 'MR'));

            $context = array(
                'firms' => Firm::model()->getList(Yii::app()->session['selected_institution_id'], $subspecialty->id),
                'date_from' => $date_from,
                'date_to' => $date_to,
            );

            Audit::add('Reports', 'view', "<pre>" . print_r(['report-name' => 'Therapy applications'], true) . "</pre>");
            $this->pageTitle = 'Therapy Application report';
            $this->render('applications', $context);
        }
    }

    protected function getApplications($date_from, $date_to, $firm = null, $institution_id)
    {
        $command = Yii::app()->db->createCommand()
            ->select(
                "p.id as patient_id, diag.left_diagnosis1_id, diag.left_diagnosis2_id, diag.right_diagnosis1_id, diag.right_diagnosis2_id, e.id,
						c.first_name, c.last_name, e.created_date, p.gender, p.dob, eye.name AS eye, site.name as site_name,
						firm.name as firm_name, concat(uc.first_name, ' ', uc.last_name) as created_user,
						ps.left_treatment_id, ps.right_treatment_id, ps.left_nice_compliance, ps.right_nice_compliance"
            )
            ->from('et_ophcotherapya_therapydiag diag')
            ->join('event e', 'e.id = diag.event_id')
            ->join('user u', 'u.id = e.created_user_id')
            ->join('contact uc', 'uc.id = u.contact_id')
            ->join('episode ep', 'e.episode_id = ep.id')
            ->join('patient p', 'ep.patient_id = p.id')
            ->join('contact c', 'p.contact_id = c.id')
            ->join('eye', 'eye.id = diag.eye_id')
            ->join('et_ophcotherapya_mrservicein mrinfo', 'mrinfo.event_id = diag.event_id')
            ->leftJoin('site', 'mrinfo.site_id = site.id')// in earlier instances of therapy application, site was not set
            ->join('firm', 'mrinfo.consultant_id = firm.id')
            ->join('et_ophcotherapya_patientsuit ps', 'diag.event_id = ps.event_id')
            ->where('e.deleted = 0 and ep.deleted = 0 and e.created_date >= :from_date and e.created_date < (:to_date + interval 1 day)')
            ->order('e.created_date asc');
        $params = array(':from_date' => $date_from, ':to_date' => $date_to);

        if ($firm) {
            $command->andWhere(
                '(mrinfo.consultant_id = :consultant_id)'
            );
            $params[':consultant_id'] = $firm->id;
        }

        if ($institution_id) {
            $command->andWhere('e.institution_id = :institution_id');
            $params[':institution_id'] = $institution_id;
        }

        $results = array();

        foreach ($command->queryAll(true, $params) as $row) {
            $display_primary_number_usage_code = SettingMetadata::model()->getSetting('display_primary_number_usage_code');
            $patient_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution($display_primary_number_usage_code, Institution::model()->getCurrent()->id, $this->selectedSiteId);
            $patient_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $row['patient_id'], Institution::model()->getCurrent()->id, $this->selectedSiteId));
            $patient_identifiers = PatientIdentifierHelper::getAllPatientIdentifiersForReports($row['patient_id']);

            $record = array(
                'application_date' => date('j M Y', strtotime($row['created_date'])),
                $patient_identifier_prompt => $patient_identifier_value,
                'patient_firstname' => $row['first_name'],
                'patient_surname' => $row['last_name'],
                'patient_gender' => $row['gender'],
                'patient_dob' => date('j M Y', strtotime($row['dob'])),
                'eye' => $row['eye'],
                'site_name' => ($row['site_name']) ? $row['site_name'] : 'N/A',
                'consultant' => $row['firm_name'],
                'created_user' => $row['created_user'],
                'left_diagnosis' => $this->getDiagnosisString($row['left_diagnosis1_id']),
                'left_secondary_to' => $this->getDiagnosisString($row['left_diagnosis2_id']),
                'right_diagnosis' => $this->getDiagnosisString($row['right_diagnosis1_id']),
                'right_secondary_to' => $this->getDiagnosisString($row['right_diagnosis2_id']),
                'left_treatment' => $this->getTreatmentString($row['left_treatment_id']),
                'right_treatment' => $this->getTreatmentString($row['right_treatment_id']),
                'left_compliant' => $this->sideCompliance('left', $row),
                'right_compliant' => $this->sideCompliance('right', $row),
            );

            $this->appendSubmissionValues($record, $row['id']);
            $this->appendInjectionValues($record, $row['patient_id'], $row['left_treatment_id'], $row['right_treatment_id']);

            $record['patient_ids'] = $patient_identifiers;

            $results[] = $record;
        }

        return $results;
    }

    /**
     * Get the compliance string for the given side on the data $row.
     *
     * @param $side
     * @param $row
     *
     * @return string
     */
    protected function sideCompliance($side, $row)
    {
        if ($row[$side . '_treatment_id']) {
            return $row[$side . '_nice_compliance'] ? 'Y' : 'N';
        } else {
            return 'N/A';
        }
    }

    protected $_diagnosis_cache = array();

    /**
     * @param $diagnosis_id
     *
     * @return string
     */
    protected function getDiagnosisString($diagnosis_id)
    {
        if (!$diagnosis_id) {
            return 'N/A';
        }
        if (!@$this->_diagnosis_cache[$diagnosis_id]) {
            $disorder = Disorder::model()->findByPk($diagnosis_id);
            if ($disorder) {
                $this->_diagnosis_cache[$diagnosis_id] = $disorder->term;
            } else {
                $this->_diagnosis_cache[$diagnosis_id] = 'REMOVED DISORDER';
            }
        }

        return $this->_diagnosis_cache[$diagnosis_id];
    }

    protected $_treatment_cache = array();

    protected function getTreatment($treatment_id)
    {
        if (!@$this->_treatment_cache[$treatment_id]) {
            $this->_treatment_cache[$treatment_id] = OphCoTherapyapplication_Treatment::model()->findByPk($treatment_id);
        }

        return $this->_treatment_cache[$treatment_id];
    }

    /**
     * @param $treatment_id
     *
     * @return string
     */
    protected function getTreatmentString($treatment_id)
    {
        if (!$treatment_id) {
            return 'N/A';
        }
        if ($treatment = $this->getTreatment($treatment_id)) {
            return $treatment->getName();
        }

        return 'REMOVED TREATMENT';
    }

    /**
     * Appends information about the submission of the application to the $record.
     *
     * @param array $record
     * @param int   $event_id
     */
    protected function appendSubmissionValues(&$record, $event_id)
    {
        if (@$_GET['submission']) {
            $event = Event::model()->findByPk($event_id);
            $svc = new OphCoTherapyapplication_Processor($event);
            $record['submission_status'] = $svc->getApplicationStatus();
            if ($record['submission_status'] == OphCoTherapyapplication_Processor::STATUS_SENT) {
                $most_recent = OphCoTherapyapplication_Email::model()->forEvent($event)->unarchived()->findAll(array('limit' => 1));
                $record['submission_date'] = Helper::convertDate2NHS($most_recent[0]->created_date);
            } else {
                $record['submission_date'] = 'N/A';
            }
        }
    }

    protected $_patient_cache = array();

    protected function getPatient($patient_id = null)
    {
        if ($patient_id === null) {
            return null;
        }

        if (!@$this->_patient_cache[$patient_id]) {
            $this->_patient_cache[$patient_id] = Patient::model()->noPas()->findByPk($patient_id);
        }

        return $this->_patient_cache[$patient_id];
    }

    protected function appendInjectionValues(&$record, $patient_id, $left_treatment_id = null, $right_treatment_id = null)
    {
        $last_columns = array('last_injection_site', 'last_injection_date', 'last_injection_number');

        if (@$_GET['last_injection']) {
            foreach (array('left', 'right') as $side) {
                #initialise columns
                foreach ($last_columns as $col) {
                    $record[$side . '_' . $col] = 'N/A';
                }
                if ($treatment_id = ${$side . '_treatment_id'}) {
                    $treatment = $this->getTreatment($treatment_id);
                    if (!$treatment || !$treatment->drug_id) {
                        continue;
                    }

                    $command = Yii::app()->db->createCommand()
                        ->select(
                            'treat.' . $side . '_number as last_injection_number, treat.created_date as last_injection_date, site.name as last_injection_site'
                        )
                        ->from('et_ophtrintravitinjection_treatment treat')
                        ->join('et_ophtrintravitinjection_site insite', 'insite.event_id = treat.event_id')
                        ->join('site', 'insite.site_id = site.id')
                        ->join('event e', 'e.id = treat.event_id')
                        ->join('episode ep', 'e.episode_id = ep.id')
                        ->where(
                            'e.deleted = 0 and ep.deleted = 0 and ep.patient_id = :patient_id and treat.' . $side . '_drug_id = :drug_id',
                            array(':patient_id' => $patient_id, ':drug_id' => $treatment->drug_id)
                        )
                        ->order('treat.created_date desc')
                        ->limit(1);

                    $res = $command->queryRow();
                    if ($res) {
                        foreach ($last_columns as $col) {
                            $record[$side . '_' . $col] = Helper::convertMySQL2NHS($res[$col], $res[$col]);
                        }
                    }
                }
            }
        }
        if (@$_GET['last_injection']) {
            foreach (array('left', 'right') as $side) {
                $record[$side . '_first_injection_date'] = 'N/A';

                if ($treatment_id = ${$side . '_treatment_id'}) {
                    $treatment = $this->getTreatment($treatment_id);
                    if (!$treatment || !$treatment->drug_id) {
                        continue;
                    }

                    $command = Yii::app()->db->createCommand()
                        ->select(
                            'treat.created_date as first_injection_date'
                        )
                        ->from('et_ophtrintravitinjection_treatment treat')
                        ->join('event e', 'e.id = treat.event_id')
                        ->join('episode ep', 'e.episode_id = ep.id')
                        ->where(
                            'e.deleted = 0 and ep.deleted = 0 and ep.patient_id = :patient_id and treat.' . $side . '_drug_id = :drug_id',
                            array(':patient_id' => $patient_id, ':drug_id' => $treatment->drug_id)
                        )
                        ->order('treat.created_date asc')
                        ->limit(1);

                    $res = $command->queryRow();
                    if ($res) {
                        $record[$side . '_first_injection_date'] = Helper::convertMySQL2NHS($res['first_injection_date']);
                    }
                }
            }
        }
    }

    public function actionPendingApplications()
    {
        $sent = false;
        $get = array('report-name' => 'Pending Therapy applications') + $_GET;


        if (Yii::app()->getRequest()->getQuery('report') === 'generate') {
            $institution_id = Yii::app()->request->getParam('institution_id', null);

            $pendingApplications = new PendingApplications();

            try {
                $sent = $pendingApplications->emailCsvFile(Yii::app()->params['applications_alert_recipients'], $institution_id);
                $get['success'] = ($sent ? 'Email sent to addresses defined in config "applications_alert_recipients"' : 'Email sending failed.');
            } catch (Exception $e) {
                \Yii::app()->user->setFlash('error.error', "Failed to send report email.");
                \OELog::log($e->getMessage());
                $get['error'] = $e->getMessage();
            } finally {
                Audit::add('Reports', 'send', "<pre>" . print_r($get, true) . "</pre>");
            }
        } else {
            Audit::add('Reports', 'view', "<pre>" . print_r($get, true) . "</pre>");
        }

        $this->pageTitle = 'Pending Applications report';
        $this->render('pending_applications', array('sent' => $sent));
    }

    public function canUseTherapyReport()
    {
        $has_role = Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id);
        $is_consultant = Firm::model()->findByAttributes(array('consultant_id' => Yii::app()->user->id));

        return $has_role || $is_consultant;
    }
}
