<?php

namespace OEModule\PASAPI\controllers;

/*
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

use OEModule\PASAPI\models\PasApiAssignment;
use OEModule\PASAPI\resources\BaseResource;
use OEModule\PASAPI\resources\Patient;
use OEModule\PASAPI\resources\PatientAppointment;
use OEModule\PASAPI\resources\PatientMerge;
use PatientIdentifier;
use UserIdentity;

class V1Controller extends \CController
{
    protected static $resources = array('Patient', 'PatientAppointment', 'PatientMerge');
    protected static $version = 'V1';
    protected static $supported_formats = array('xml');

    public static $UPDATE_ONLY_HEADER = 'HTTP_X_OE_UPDATE_ONLY';
    public static $PARTIAL_RECORD_HEADER = 'HTTP_X_OE_PARTIAL_RECORD';

    /**
     * @var string output format defaults to xml
     */
    protected $output_format = 'xml';

    /**
     * @TODO: map from output_format when we support multiple.
     *
     * @return string
     */
    protected function getContentType()
    {
        return 'application/xml';
    }

    /**
     * This overrides the default behaviour for supported resources by pushing the resource
     * into the GET parameter and updating the actionID.
     *
     * This is necessary because there's no way of pushing the appropriate pattern to the top of the
     * URLManager config, so this captures calls where the id doesn't contain non-numerics.
     *
     * @param string $actionID
     * @return \CAction|\CInlineAction
     * @throws \CException
     */
    public function createAction($actionID)
    {
        if (in_array($actionID, static::$resources)) {
            $_GET['resource_type'] = $actionID;
            switch (\Yii::app()->getRequest()->getRequestType()) {
                case 'PUT':
                    return parent::createAction('Update');
                    break;
                case 'DELETE':
                    return parent::createAction('Delete');
                    break;
                default:
                    $this->sendResponse(405);
                    break;
            }
        }

        return parent::createAction($actionID);
    }

    /**
     * @param \CAction $action
     *
     * @return bool
     */
    public function beforeAction($action)
    {
        foreach (\Yii::app()->request->preferredAcceptTypes as $type) {
            if ($type['baseType'] == 'xml' || $type['subType'] == 'xml' || $type['subType'] == '*') {
                $this->output_format = 'xml';
                break;
            } else {
                $this->output_format = $type['baseType'];
            }
        }

        if (!in_array($this->output_format, static::$supported_formats)) {
            $this->sendResponse(406, 'PASAPI only supports ' . implode(',', static::$supported_formats));
        }

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            $this->sendResponse(401);
        }

        $identity = new UserIdentity($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], null, null);
        if (!$identity->authenticate()) {
            $this->sendResponse(401);
        }

        \Yii::app()->user->login($identity);

        if (!\Yii::app()->user->checkAccess('OprnApi')) {
            $this->sendResponse(403);
        }

        foreach (\SettingMetadata::model()->findAll() as $metadata) {
            if (!$metadata->element_type && !isset(\Yii::app()->params[$metadata->key])) {
                \Yii::app()->params[$metadata->key] = $metadata->getSetting($metadata->key);
            }
        }

        return parent::beforeAction($action);
    }

    /**
     * Simple wrapper to encapsulate the arguments required for any of the API actions.
     */
    public function expectedParametersForAction($action)
    {
        return array(
            'update' => 'id',
            'delete' => 'id',
        )[strtolower($action->id)];
    }

    /**
     * @param \CAction $action
     *
     * @throws CHttpException
     */
    public function invalidActionParams($action)
    {
        $this->sendErrorResponse(400, array('Missing request parameter(s). Required parameter(s) are: ' . $this->expectedParametersForAction($action)));
    }

    public function getResourceModel($resource_type)
    {
        return "\\OEModule\\PASAPI\\resources\\{$resource_type}";
    }

    /**
     * Whether the request is an "updateOnly" request.
     *
     * @return bool
     */
    public function getUpdateOnly()
    {
        if (!array_key_exists(static::$UPDATE_ONLY_HEADER, $_SERVER)) {
            return false;
        }

        return (bool)$_SERVER[static::$UPDATE_ONLY_HEADER];
    }

    /**
     * Whether the request is a partial record which only sets the fields that are provided
     * on the given record.
     *
     * @return bool
     */
    public function getPartialRecord()
    {
        if (!array_key_exists(static::$PARTIAL_RECORD_HEADER, $_SERVER)) {
            return false;
        }

        return (bool)$_SERVER[static::$PARTIAL_RECORD_HEADER];
    }

    /**
     * @param $resource_type
     * @param $id
     */
    public function actionUpdate($resource_type, $id, $identifier_type)
    {
        if (!in_array($resource_type, static::$resources)) {
            $this->sendErrorResponse(404, "Unrecognised Resource type {$resource_type}");
        }

        if (!$id) {
            $this->sendErrorResponse(404, ['External Resource ID required']);
        }

        $resource_model = $this->getResourceModel($resource_type);

        $body = \Yii::app()->request->rawBody;
        $patient_identifier_type = \PatientIdentifierType::model()->findByAttributes(['unique_row_string' => $identifier_type]);
        if ($patient_identifier_type) {
            \Yii::app()->session["selected_institution_id"] = $patient_identifier_type->institution_id;
        }

        try {
            /** @var BaseResource $resource */
            $resource = $resource_model::fromXml(static::$version, $body, array(
                'update_only' => $this->getUpdateOnly(),
                'partial_record' => $this->getPartialRecord(),
            ));

            $resource->id = $id; // LOCAL number

            switch ($resource_type) {
                case "Patient":
                    /** @var Patient $resource */
                    $this->validatePatientResource($resource, $id, $identifier_type);
                    break;

                case "PatientAppointment":
                    /** @var PatientAppointment $resource */
                    $resource->setPatientIdentifierType($patient_identifier_type);
                    break;

                case "PatientMerge":
                    /** @var PatientMerge $resource */
                    $resource->setPatientIdentifierType($patient_identifier_type)
                             ->setAndValidatePatients();
                    break;
            }

            if ($resource->errors) {
                $this->sendErrorResponse(400, $resource->errors);
            }

            $internal_id = $resource->save(); // $internal_id is the patient.id , protected/models/Patient's id
            if (!$internal_id) {
                if ($resource->errors && !$resource->warn_errors) {
                    $this->sendErrorResponse(400, $resource->errors);
                } else {
                    // no internal id indicates we didn't get a resource
                    $response = array('Message' => $resource_type . ' not created');
                    // map errors to warnings if this is the case
                    if ($resource->errors) {
                        $response['Warnings'] = $resource->errors;
                    }

                    // success in that we are happy for there to have been no action taken
                    $this->sendSuccessResponse(200, $response);
                }
            }

            $transaction = \Yii::app()->db->beginTransaction();

            //@ TODO: Test for PatientAppointment
            // this is \PASAPI\resources\Patient
            if ($resource instanceof Patient) {
                // Resource and Patient are saved at this point


                $assignment = $resource->getAssignment();
                $patient = $assignment->getInternal();

                // Let's add PatientIdentifiers
                $criteria = new \CDbCriteria();
                $criteria->addCondition('value = :value');
                $criteria->addCondition('patient_id = :patient_id');
                $criteria->addCondition('patient_identifier_type_id = :type_id');
                $criteria->params[':value'] = $id;
                $criteria->params[':type_id'] = $patient_identifier_type->id;
                $criteria->params[':patient_id'] = $patient->id;

                $patient_has_this_number = \PatientIdentifier::model()->disableDefaultScope()->count($criteria);

                if (!$patient_has_this_number) {
                    // patient/value/type combination is not in the DB, we can add it
                    // using the $id here because the crossCheck function can change the assignment (aka Patient)
                    \PatientIdentifierHelper::addNumberToPatient($patient, $patient_identifier_type, $id);
                } else {
                    // Patient already has this type/value, nothing to do, it is a simple update
                }

                $global_institution_id = \PatientIdentifierHelper::getGlobalInstitutionIdFromSetting();
                $global_patient_identifier = \PatientIdentifierHelper::getIdentifierForPatient('GLOBAL', $patient->id, $global_institution_id, null, true);


                // NHS number update
                if ($global_patient_identifier) {
                    // Check if the number exist
                    $criteria = new \CDbCriteria();
                    $criteria->addCondition('value = :value');
                    $criteria->addCondition('patient_identifier_type_id = :type_id');
                    $criteria->addCondition('source_info = :source_info');
                    $criteria->addCondition('deleted = :deleted');
                    $criteria->addCondition('patient_id != :patient_id');
                    $criteria->params[':value'] = $resource->getAssignedProperty('NHSNumber');
                    $criteria->params[':type_id'] = $global_patient_identifier->patient_identifier_type_id;
                    $criteria->params[':source_info'] = \PatientIdentifierHelper::PATIENT_IDENTIFIER_ACTIVE_SOURCE_INFO;
                    $criteria->params[':deleted'] = 0;
                    $criteria->params[':patient_id'] = $patient->id;

                    $duplicate_patient_identifier = \PatientIdentifier::model()->find($criteria);

                    if ($duplicate_patient_identifier) {
                        $duplicate_patient_identifier->deleted = 1;
                        $duplicate_patient_identifier->source_info = \PatientIdentifierHelper::PATIENT_IDENTIFIER_DELETED_BY_STRING . $patient->id . '[' . time() . ']';
                        $duplicate_patient_identifier->save();
                    }

                    if ($global_patient_identifier->value !== $resource->getAssignedProperty('NHSNumber')) {
                            // here we can update the NHS number
                            $global_patient_identifier->value = $resource->getAssignedProperty('NHSNumber');
                            $global_patient_identifier->update(['value']);
                    } else {
                        // values are equal, nothing to do
                    }

                    $global_patient_identifier->source_info = \PatientIdentifierHelper::PATIENT_IDENTIFIER_ACTIVE_SOURCE_INFO;
                    $global_patient_identifier->deleted = 0;
                    $global_patient_identifier->update(['source_info','deleted']);
                } else {
                    $global_number = $resource->getAssignedProperty('NHSNumber');
                    if (!empty($global_number)) {
                            $global_type = \PatientIdentifierHelper::getCurrentGlobalType();
                            \PatientIdentifierHelper::addNumberToPatient($patient, $global_type, $resource->getAssignedProperty('NHSNumber'));
                    }
                }
                $resource->addGlobalNumberStatus($patient);
            }

            $transaction->commit();

            $response = array(
                'Id' => $internal_id,
            );

            if ($resource->isNewResource) {
                $status_code = 201;
                $response['Message'] = $resource_type . ' created.';
            } else {
                $status_code = 200;
                $response['Message'] = $resource_type . ' updated.';
            }

            if ($resource->warnings) {
                $response['Warnings'] = $resource->warnings;
            }

            $this->sendSuccessResponse($status_code, $response);
        } catch (\Exception $e) {
            $errors = isset($resource) ? $resource->errors : [];
            $errors[] = $e->getMessage();
            $errors[] = $e->getTraceAsString();

            if (isset($transaction)) {
                $transaction->rollback();
            }

            $this->sendErrorResponse(500, $errors);
        }
    }

    private function getPasApiAssignment(Patient $resource, $patient_identifier_value, $identifier_type): ?PasApiAssignment
    {
        $_assignment = null;
        $patient = null;
        $existing_patient_identifier = PatientIdentifier::model()->find(
            'value=:value AND patient_identifier_type_id=:patient_identifier_type_id',
            [':value' => $patient_identifier_value,
                ':patient_identifier_type_id' => $identifier_type->id]
        );
        if ($existing_patient_identifier) {
            $patient = $existing_patient_identifier->patient;
        } else {
            // New patient resource we have here guys
            // We need to check if this patient already exist under another number space (PatientIdentifierType)
            $global_number = $resource->getAssignedProperty('NHSNumber');
            if (!empty($global_number)) {
                $gender = strtoupper($resource->getAssignedProperty('Gender'));
                $dob = $resource->getAssignedProperty('DateOfBirth');

                $global_type = \PatientIdentifierHelper::getCurrentGlobalType();

                $criteria = new \CDbCriteria();
                $criteria->join = 'JOIN patient_identifier pi ON pi.patient_id = t.id';
                $criteria->addCondition('patient_identifier_type_id = :patient_identifier_type_id');
                $criteria->addCondition('pi.value = :value');
                $criteria->addCondition('t.dob = :dob');
                $criteria->addCondition('t.gender = :gender');
                $criteria->params[':patient_identifier_type_id'] = $global_type->id;
                $criteria->params[':value'] = $global_number;
                $criteria->params[':gender'] = $gender;
                $criteria->params[':dob'] = date('Y-m-d', strtotime($dob));

                $patient = \Patient::model()->find($criteria);
            }
        }

        // based on GLOBAL (NHS) number, DOB and gender we can assume that this is our patient under a different Type
        // Find this \Patient in PAS assignment to get the Resource object
        if ($patient) {
            $_assignment = PasApiAssignment::model()->findByAttributes([
                'resource_type' => 'Patient',
                'internal_type' => '\Patient',
                'internal_id' => $patient->id
            ]);
        }

        return $_assignment;
    }

    public function actionDelete($resource_type, $id)
    {
        if (!in_array($resource_type, static::$resources)) {
            $this->sendErrorResponse(404, "Unrecognised Resource type {$resource_type}");
        }

        if (!$id) {
            $this->sendResponse(404, 'External Resource ID required');
        }

        $resource_model = $this->getResourceModel($resource_type);

        if (!method_exists($resource_model, 'delete')) {
            $this->sendResponse(405);
        }

        try {
            if (!$resource = $resource_model::fromResourceId(static::$version, $id)) {
                $this->sendResponse(404, 'Could not find resource for external Id');
            }

            if ($resource->delete()) {
                $this->sendResponse(204);
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();

            $this->sendErrorResponse(500, $errors);
        }
    }

    protected function sendErrorResponse($status, $messages = array())
    {
        $body = '<Failure><Errors><Error>' . implode('</Error><Error>', $messages) . '</Error></Errors></Failure>';

        $this->sendResponse($status, $body);
    }

    protected function sendSuccessResponse($status, $response)
    {
        $body = '<Success>';
        if (isset($response['Id'])) {
            $body .= "<Id>{$response['Id']}</Id>";
        }

        $body .= "<Message>{$response['Message']}</Message>";

        if (isset($response['Warnings'])) {
            $body .= '<Warnings><Warning>' . implode('</Warning><Warning>', $response['Warnings']) . '</Warning></Warnings>';
        }

        $body .= '</Success>';

        $this->sendResponse($status, $body);
    }

    protected function sendResponse($status = 200, $body = '')
    {
        header('HTTP/1.1 ' . $status);
        header('Content-type: ' . $this->getContentType());
        if ($status == 401) {
            header('WWW-Authenticate: Basic realm="OpenEyes"');
        }
        // TODO: configure allowed methods per resource
        if ($status == 405) {
            header('Allow: PUT');
        }
        echo $body;
        \Yii::app()->end();
    }

    private function validatePatientResource($resource, $id, $identifier_type)
    {
        $patient_identifier_type = \PatientIdentifierType::model()->findByAttributes(['unique_row_string' => $identifier_type]);

        if (!$patient_identifier_type) {
            $this->sendErrorResponse(404, ['PatientIdentifierType cannot be found based on "identifier-type": ' . $identifier_type]);
        }

        // validate the local number
        $is_id_valid = $patient_identifier_type->validateTerm($id);
        if ($is_id_valid === false) {
            $this->sendErrorResponse(422, ["Patient number (format) in request URL is invalid: '{$id}'. Acceptable: {$patient_identifier_type->validate_regex}"]);
        }

        $global_type = \PatientIdentifierHelper::getCurrentGlobalType();

        $pas_api_assignment = $this->getPasApiAssignment($resource, $id, $patient_identifier_type);


        if ($pas_api_assignment) {
            // now basically we assign the existing \Patient object to the new Resource
            // whit this Resource(xml) properties will update the existing Patient object properties
            $resource->setAssignment($pas_api_assignment);
        }
        // validate NHSNumber in XML
        $xml_nhs_num = $resource->getAssignedProperty('NHSNumber');
        $is_global_num_valid = $global_type->validateTerm($xml_nhs_num, true);
        if (!$is_global_num_valid) {
            $this->sendErrorResponse(422, ["Patient NHSNumber (format) in XML is invalid: '{$xml_nhs_num}'. Acceptable: {$global_type->validate_regex}"]);
        }

        // validate HospitalNumber in XML
        $hos_num = $resource->getAssignedProperty('HospitalNumber');
        $is_id_valid = $patient_identifier_type->validateTerm($hos_num);
        if ($is_id_valid === false) {
            $this->sendErrorResponse(422, ["Patient number (format) in XML is invalid: '{$hos_num}'. Acceptable: {$patient_identifier_type->validate_regex}"]);
        }

        if ($hos_num !== $id) {
            $this->sendErrorResponse(422, ["HospitalNumber in URL and in XML must match: {$hos_num} not equal to {$id}"]);
        }

        $assignment = $resource->getAssignment();
        $patient = $assignment->getInternal();

        if ($patient->id) {
            // Check if the patient has an active number in this namespace
            $criteria = new \CDbCriteria();
            $criteria->addCondition('patient_id = :patient_id');
            $criteria->addCondition('patient_identifier_type_id = :type_id');
            $criteria->addCondition('value != :value');
            $criteria->params[':patient_id'] = $patient->id;
            $criteria->params[':type_id'] = $patient_identifier_type->id;
            $criteria->params[':value'] = $resource->getAssignedProperty('HospitalNumber');


            $patient_in_numberspace = \PatientIdentifier::model()->count($criteria);
            if ($patient_in_numberspace) {
                $this->sendErrorResponse(422, ["This patient has a different HospitalNumber"]);
            }
        } else {
            // check if the value/patient_identifier_type pair is in DB for another patient
            $criteria = new \CDbCriteria();
            $criteria->addCondition('value = :value');
            $criteria->addCondition('patient_identifier_type_id = :type_id');
            $criteria->params[':value'] = $resource->getAssignedProperty('HospitalNumber');
            $criteria->params[':type_id'] = $patient_identifier_type->id;

            $value_numberspace_exist = \PatientIdentifier::model()->count($criteria);

            if ($value_numberspace_exist) {
                $this->sendErrorResponse(422, ["HospitalNumber belongs to another patient"]);
            }
        }
    }
}
