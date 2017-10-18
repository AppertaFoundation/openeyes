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
require_once 'Zend/Http/Client.php';

class PortalExamsCommand extends CConsoleCommand
{
    protected $client;

    protected $config = array();

    public function run($args)
    {
        $creator = new OEModule\OphCiExamination\components\ExaminationCreator();
        $user = new User();
        $connection = new OptomPortalConnection();
        $this->client = $connection->getClient();
        $this->config = $connection->getConfig();
        $examinations = $this->examinationSearch();

        $defaultInvoiceStatus = \OEModule\OphCiExamination\models\InvoiceStatus::model()->findByAttributes( array('name' => 'No status'));

        $eventType = EventType::model()->find('name = "Examination"');
        $portalUser = $user->portalUser();
        if (!$portalUser) {
            throw new Exception('No User found for import');
        }
        $portalUserId = $portalUser->id;

        $refractionType = \OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type::model()->find('name = "Ophthalmologist"');

        $eyes = Eye::model()->findAll();
        $eyeIds = array();
        foreach ($eyes as $eye) {
            $eyeIds[strtolower($eye->name)] = $eye->id;
        }

        foreach ($examinations as $examination) {
            $uidArray = explode('-', $examination['patient']['unique_identifier']);
            $uniqueCode = $uidArray[1];
            $opNoteEvent = UniqueCodes::model()->eventFromUniqueCode($uniqueCode);
            $examinationEventLog = new AutomaticExaminationEventLog();
            if (!$opNoteEvent) {
                echo 'No Event found for identifier: '.$examination['patient']['unique_identifier'].PHP_EOL;
                $existingUnfound = $examinationEventLog->findByAttributes(array('unique_code' => $uniqueCode));
                if ($existingUnfound) {
                    $examinationEventLog = $existingUnfound;
                }
                $examinationEventLog->unique_code = $uniqueCode;
                $examinationEventLog->event_id = 0;
                $examinationEventLog->examination_date = $examination['examination_date'];
                $examinationEventLog->examination_data = json_encode($examination);
                $examinationEventLog->invoice_status_id = $defaultInvoiceStatus->id;

                $importStatus = ImportStatus::model()->find('status_value = "Unfound Event"');
                $examinationEventLog->import_success = $importStatus->id;
                $examinationEventLog->optometrist = $examination['op_tom']['name'];
                $examinationEventLog->goc_number = $examination['op_tom']['goc_number'];
                $examinationEventLog->optometrist_address = $examination['op_tom']['address'];
                if (!$examinationEventLog->save()) {
                    echo '$examination_event_log failed: '.print_r($examinationEventLog->getErrors(), true);
                }
                continue;
            }
            $duplicateRecord = UniqueCodes::model()->examinationEventCheckFromUniqueCode($uniqueCode);
            if (($duplicateRecord['count'] < 1)) {
                $transaction = $opNoteEvent->getDbConnection()->beginInternalTransaction();

                try {
                    $examinationEvent = $creator->save($opNoteEvent->episode_id, $portalUserId, $examination, $eventType, $eyeIds, $refractionType, $opNoteEvent->id);
                } catch (Exception $e) {
                    $transaction->rollback();
                    $importStatus = ImportStatus::model()->find('status_value = "Import Failure"');
                    $examinationEventLog->event_id = 0;
                    $examinationEventLog->unique_code = $uniqueCode;
                    $examinationEventLog->examination_date = $examination['examination_date'];
                    $examinationEventLog->examination_data = json_encode($examination);
                    $examinationEventLog->invoice_status_id = $defaultInvoiceStatus->id;

                    $examinationEventLog->import_success = $importStatus->id;
                    $examinationEventLog->optometrist = $examination['op_tom']['name'];
                    $examinationEventLog->goc_number = $examination['op_tom']['goc_number'];
                    $examinationEventLog->optometrist_address = $examination['op_tom']['address'];
                    if (!$examinationEventLog->save()) {
                        throw new CDbException('$examination_event_log failed: '.print_r($examinationEventLog->getErrors(), true));
                    }
                    echo 'Failed for examination '.$examination['patient']['unique_identifier'].' with exception: '.
                        $e->getMessage().'on line '.$e->getLine().' in file '.$e->getFile().PHP_EOL.$e->getTraceAsString();
                    continue;
                }
                $importStatus = ImportStatus::model()->find('status_value = "Success Event"');
                $examinationEventLog->event_id = ($examinationEvent->id) ? $examinationEvent->id : 0;
                $examinationEventLog->unique_code = $uniqueCode;
                $examinationEventLog->examination_date = $examination['examination_date'];
                $examinationEventLog->examination_data = json_encode($examination);
                $examinationEventLog->invoice_status_id = $defaultInvoiceStatus->id;

                $examinationEventLog->import_success = $importStatus->id;
                $examinationEventLog->optometrist = $examination['op_tom']['name'];
                $examinationEventLog->goc_number = $examination['op_tom']['goc_number'];
                $examinationEventLog->optometrist_address = $examination['op_tom']['address'];
                if (!$examinationEventLog->save()) {
                    throw new CDbException('$examination_event_log failed: '.print_r($examinationEventLog->getErrors(), true));
                }
                $transaction->commit();
                echo 'Examination imported: '.$examinationEvent->id.PHP_EOL;
            } else {
                if ($duplicateRecord['examination_data'] !== json_encode($examination)) {
                    $eventType = EventType::model()->find('name = "Examination"');
                    $examinationEventLog->event_id = $duplicateRecord['event_id'];
                    $examinationEventLog->unique_code = $uniqueCode;
                    $examinationEventLog->examination_date = $examination['examination_date'];
                    $examinationEventLog->examination_data = json_encode($examination);
                    $examinationEventLog->invoice_status_id = $defaultInvoiceStatus->id;

                    $importStatus = ImportStatus::model()->find('status_value = "Duplicate Event"');
                    $examinationEventLog->import_success = $importStatus->id;
                    $examinationEventLog->optometrist = $examination['op_tom']['name'];
                    $examinationEventLog->goc_number = $examination['op_tom']['goc_number'];
                    $examinationEventLog->optometrist_address = $examination['op_tom']['address'];
                    echo 'Duplicate record found for '.$examination['patient']['unique_identifier'].PHP_EOL;
                    if (!$examinationEventLog->save()) {
                        echo '$examination_event_log failed: '.print_r($examinationEventLog->getErrors(), true).PHP_EOL;
                    }
                }
            }
        }
    }

    /**
     * Search the API for examinations.
     *
     * @return mixed
     */
    protected function examinationSearch()
    {
        $this->client->setUri($this->config['uri'].$this->config['endpoints']['examinations']);
        $eventLog = new AutomaticExaminationEventLog();
        $last = $eventLog->latestSuccessfulEvent();
        if ($last) {
            $lastExam = json_decode($last->examination_data);
            $this->client->setParameterPost(array('start_date' => $lastExam->updated_at));
        }
        $response = $this->client->request('POST');

        return json_decode($response->getBody(), true);
    }
}
