<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
require_once 'Zend/Http/Client.php';

class PortalExamsCommand extends CConsoleCommand
{
    protected $client;

    protected $config = array();

    public function run()
    {
        $creator = new OEModule\OphCiExamination\components\ExaminationCreator();
        $user = new User();
        $this->setConfig();
        $this->client = $this->initClient();
        $this->login();
        $examinations = $this->examinationSearch();

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
                $importStatus = ImportStatus::model()->find('status_value = "Unfound Event"');
                $examinationEventLog->import_success = $importStatus->id;
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
                    $examinationEventLog->import_success = $importStatus->id;
                    if (!$examinationEventLog->save()) {
                        throw new CDbException('$examination_event_log failed: '.print_r($examinationEventLog->getErrors(), true));
                    }
                    echo 'Failed for examination '.$examination['patient']['unique_identifier'].' with exception: '.$e->getMessage().'on line '.$e->getLine().' in file '.$e->getFile().PHP_EOL.$e->getTraceAsString();
                    continue;
                }
                $importStatus = ImportStatus::model()->find('status_value = "Success Event"');
                $examinationEventLog->event_id = ($examinationEvent->id) ? $examinationEvent->id : 0;
                $examinationEventLog->unique_code = $uniqueCode;
                $examinationEventLog->examination_date = $examination['examination_date'];
                $examinationEventLog->examination_data = json_encode($examination);
                $examinationEventLog->import_success = $importStatus->id;
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
                    $importStatus = ImportStatus::model()->find('status_value = "Duplicate Event"');
                    $examinationEventLog->import_success = $importStatus->id;
                    echo 'Duplicate record found for '.$examination['patient']['unique_identifier'].PHP_EOL;
                    if (!$examinationEventLog->save()) {
                        echo '$examination_event_log failed: '.print_r($examinationEventLog->getErrors(), true).PHP_EOL;
                    }
                }
            }
        }
    }

    /**
     * Set portal config.
     */
    protected function setConfig()
    {
        $this->config = Yii::app()->params['portal'];
    }

    /**
     * Init HTTP client.
     *
     * @return Zend_Http_Client
     *
     * @throws Zend_Http_Client_Exception
     */
    protected function initClient()
    {
        $client = new Zend_Http_Client($this->config['uri']);
        $client->setHeaders('Accept', 'application/vnd.OpenEyesPortal.v1+json');

        return $client;
    }

    /**
     * Login to the API, set the auth header.
     */
    protected function login()
    {
        $this->client->setUri($this->config['uri'].$this->config['endpoints']['auth']);
        $this->client->setParameterPost($this->config['credentials']);
        $response = $this->client->request('POST');
        if ($response->getStatus() > 299) {
            throw new Exception('Unable to login, user credentials in config incorrect');
        }
        $jsonResponse = json_decode($response->getBody(), true);
        $this->client->resetParameters();
        $this->client->setHeaders('Authorization', 'Bearer '.$jsonResponse['access_token']);
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
