<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

use OEModule\OphCoCvi\components\OphCoCvi_Manager;

class SignController extends \BaseController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('add'),
                'users' => array('*'),
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }

    public function validateRequest($data, $event)
    {
        $errors = [];
        // in case if we want to return client error headers
        $header = 'HTTP/1.1 422 Unprocessable entity';

        if (!isset($data['unique_identifier'])) {
            $errors[] = 'Missing unique_identifier';
        }

        if (isset($data['extra_info'])) {
            $extra_info = json_decode($data['extra_info'], true);

            if (!isset($extra_info['e_id'])) {
                $errors[] = 'Missing element id.';
            }

            if (!isset($extra_info['e_t_id'])) {
                $errors[] = 'Missing element type id.';
            }
        } else {
            $errors[] = "Missing element and element type ids";
        }

        // validate the event
        if ($event) {
            $api = $event->eventType->getApi();
            if ($api && method_exists($api, 'validateUniqueCodeForEvent')) {
                if (!$api->validateUniqueCodeForEvent($event)) {
                    $errors[] = 'Invalid UniqueCode or wrong event';
                }
            } else {
                // default check
            }
        } else {
            $errors[] = 'Event not found';
        }

        return [
            'is_valid' => empty($errors),
            'header' => $header,
            'errors' => $errors
        ];
    }

    /**
     * @param array $data
     * @return \ProtectedFile
     * @throws Exception
     */
    public function saveToProtectedFile($data)
    {
        $filename = "sign_" . md5(time()) . ".png";
        $protected_file = \ProtectedFile::createForWriting($filename);
        file_put_contents($protected_file->getPath(), base64_decode($data['origImage']??$data['image']));
        $protected_file->title = "";
        $protected_file->description = "";
        $protected_file->validate();
        $protected_file->save(false);
        return $protected_file;
    }

    /**
     * @param $protected_file
     * @param $return_message
     * @return true
     */
    public function saveSignatureImportLog($protected_file, $status_id, $event = null, $return_message = '', $cropped_file_id = null)
    {
        $signature_import_log = new SignatureImportLog();
        $signature_import_log->filename = $protected_file->getPath();
        $signature_import_log->cropped_file_id = $cropped_file_id;
        $signature_import_log->status_id = $status_id;
        $signature_import_log->return_message = $return_message;
        $signature_import_log->event_id = $event->id ?? null;
        $signature_import_log->import_datetime = date('Y-m-d H:i:s');
        $signature_import_log->save(false);
        return true;
    }

    public function getOriginalProtectedFileFromLogId($log_id)
    {
        $log = \SignatureImportLog::model()->findByPk($log_id);
        $file_name = basename($log->filename);

        return \ProtectedFile::model()->findByAttributes(['uid'=>$file_name]);
    }

    public function actionAdd()
    {
        header('Access-Control-Allow-Origin: *');

        $return_message = '';
        $msg = "";

        if (\Yii::app()->request->isPostRequest) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            $event = null;
            if (isset($data['unique_identifier'])) {
                $uniq_code = \UniqueCodes::model();

                $event = $uniq_code->eventFromUniqueCode($data['unique_identifier']);
                $valid_result = $this->validateRequest($data, $event);

                if (!$valid_result['is_valid']) {
                    $return_message = implode(' ', $valid_result['errors']);
                }
            }

            // isset $data['extra_info'] and its content are validated in the validateRequest() method
            if (!isset($data['extra_info']) && !$event) {
                $return_message = "No signature data found";
            }

            // Manual crop
            if (isset($data['original_log_id'])) {
                $protected_file = $this->getOriginalProtectedFileFromLogId($data['original_log_id']);
            } else {
                $protected_file = $this->saveToProtectedFile($data);
            }

            if ($event) {
                $extra_info = json_decode($data['extra_info'], true);
                if(!empty($extra_info)){
                    $element_type = \ElementType::model()->findByPk($extra_info['e_t_id']);
                } else {
                    $element_type = null;
                }


                $element_instance = $element_type->getInstance();
                $esign_element = $element_instance->findByAttributes(['event_id' => $event->id]);
                $sign_importer = new SignImporter($event, $esign_element, $element_type, null);
                $sign_importer->setSignBase64($data['image']);
                $sign_importer->save();

                $cropped_file_id = $sign_importer->signature->signatureFile->id;

                $cvi_manager = new OphCoCvi_Manager($this->getApp());
                $cvi_manager->updateEventInfo($event);
                $return_message = $data['extra_info'] . " " . $data['unique_identifier'];
                $status_id = SignatureImportLog::STATUS_SUCCESS;

                $this->saveSignatureImportLog($protected_file, $status_id, $event, $return_message, $cropped_file_id);
                $msg .= 'Correct signature saved!';
            } else {
                $status_id = SignatureImportLog::STATUS_FAILED;
                $this->saveSignatureImportLog($protected_file, $status_id, null, $return_message);
                $msg .= 'Failed signature save!';
            }
        } else {
            $msg .= 'Bad request';
            header("HTTP/1.1 400 {$msg}");
        }

        $this->renderJSON($msg);
    }
}
