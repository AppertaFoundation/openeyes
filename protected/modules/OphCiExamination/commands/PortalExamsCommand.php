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

        $defaultInvoiceStatus = \OEModule\OphCiExamination\models\InvoiceStatus::model()->findByAttributes(array('name' => 'No status'));

        $eventType = EventType::model()->find('name = "Examination"');
        $portalUser = $user->portalUser();
        if (!$portalUser) {
            throw new Exception('No User found for import');
        }
        $portalUserId = $portalUser->id;

        $refractionType = \OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type::model()->find('name = "Optometrist"');

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
                echo 'No Event found for identifier: ' . $examination['patient']['unique_identifier'] . PHP_EOL;
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
                    echo '$examination_event_log failed: ' . print_r($examinationEventLog->getErrors(), true);
                }
                continue;
            }
            $duplicateRecord = UniqueCodes::model()->examinationEventCheckFromUniqueCode($uniqueCode);

            $auto_optom_saving_disabled = Yii::app()->params['disable_auto_import_optoms_from_portal'];
            if (isset($auto_optom_saving_disabled) && $auto_optom_saving_disabled === 'off') {
                $this->saveOptometristAsPatientContact(
                    $examination['op_tom']['name'],
                    $examination['op_tom']['address'],
                    $examination['op_tom']['goc_number'],
                    $opNoteEvent->episode->patient_id
                );
            }

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
                        throw new CDbException('$examination_event_log failed: ' . print_r($examinationEventLog->getErrors(), true));
                    }
                    echo 'Failed for examination ' . $examination['patient']['unique_identifier'] . ' with exception: ' .
                        $e->getMessage() . 'on line ' . $e->getLine() . ' in file ' . $e->getFile() . PHP_EOL . $e->getTraceAsString();
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
                    throw new CDbException('$examination_event_log failed: ' . print_r($examinationEventLog->getErrors(), true));
                }
                $transaction->commit();
                echo 'Examination imported: ' . $examinationEvent->id . PHP_EOL;
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
                    echo 'Duplicate record found for ' . $examination['patient']['unique_identifier'] . PHP_EOL;
                    if (!$examinationEventLog->save()) {
                        echo '$examination_event_log failed: ' . print_r($examinationEventLog->getErrors(), true) . PHP_EOL;
                    }
                }
            }
        }
    }

    private function saveOptometristAsPatientContact($name, $address, $goc_number, $patient_id)
    {
        $optometrist_contact = $this->getContactByNationalCodeAndAddress($goc_number, $address);

        if ($optometrist_contact == null) {
            $optometrist_contact = $this->saveOptometristContact($name, $goc_number);
        }
        $this->saveOptometristAddress($address, $optometrist_contact);
        $this->savePatientOptometristContactAssignment($patient_id, $optometrist_contact);
    }

    /**
     * Search the API for examinations.
     *
     * @return mixed
     */
    protected function examinationSearch()
    {
        $this->client->setUri($this->config['uri'] . $this->config['endpoints']['examinations']);
        $eventLog = new AutomaticExaminationEventLog();
        $last = $eventLog->latestSuccessfulEvent();
        if ($last) {
            $lastExam = json_decode($last->examination_data);
            $this->client->setParameterPost(array('start_date' => $lastExam->updated_at));
        }
        $response = $this->client->request('POST');

        return json_decode($response->getBody(), true);
    }

    private function getContactByNationalCodeAndAddress($goc_number, $address)
    {
        return \Contact::model()->with('correspondAddress')->find(
            'national_code = ? and address1 = ? ',
            [$goc_number, $address]
        );
    }

    /**
     * @param $name
     * @param $goc_number
     * @return array|Contact|mixed|null
     * @throws Exception
     */
    private function saveOptometristContact($name, $goc_number)
    {
        $optometrist_contact_label = ContactLabel::model()->find("name = ?", ['Optometrist']);
        $optometrist_contact = new Contact();
        $optometrist_contact->contact_label_id = $optometrist_contact_label->id;
        $optometrist_contact->created_institution_id = Yii::app()->session['selected_institution_id'];
        $optometrist_contact->national_code = $goc_number;
        $optometrist_contact->last_name = $name;

        $optometrist_contact->save();

        return $optometrist_contact;
    }

    /**
     * @param $address
     * @param $optometrist_contact
     * @throws Exception
     */
    private function saveOptometristAddress($address, $optometrist_contact)
    {
        if (!isset($optometrist_contact->correspondAddress) || $optometrist_contact->correspondAddress->address1 != $address) {
            $optometrist_address = new Address();
            $optometrist_address->address1 = $address;
            $optometrist_address->country_id = Address::model()->getDefaultCountryId();
            $optometrist_address->address_type_id = AddressType::model()->find('name = ? ', ['Correspondence'])->id;
            $optometrist_address->contact_id = $optometrist_contact->id;

            $optometrist_address->save();
        }
    }

    /**
     * @param $patient_id
     * @param $optometrist_contact
     * @throws Exception
     */
    private function savePatientOptometristContactAssignment($patient_id, $optometrist_contact)
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('t.patient_id = :patient_id');
        $criteria->addCondition('t.contact_id = :contact_id');
        $criteria->params[':patient_id'] = $patient_id;
        $criteria->params[':contact_id'] = $optometrist_contact->id;

        $patient = Patient::model()->findByPk($patient_id);
        $patient_contact_assignment = PatientContactAssignment::model()->find($criteria);

        if ($patient_contact_assignment == null && $patient->getPatientOptometrist() == null) {
            $patient_contact_assignment = new PatientContactAssignment();
            $patient_contact_assignment->contact_id = $optometrist_contact->id;
            $patient_contact_assignment->patient_id = $patient_id;

            $patient_contact_assignment->save();
        }
    }
}
