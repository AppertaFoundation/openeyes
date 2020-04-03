<?php
/**
 * (C) Copyright Apperta Foundation 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class RequestSaveHelper
 *
 * Usage:
 * $helper = new RequestSaveHelper($_POST);
 * $helper->save();
 *
 * or
 * $helper->loadData($_POST);
 *
 * or
 * $helper->request->system_message = 'New request arrived';
 * $helper->attachmentData->mime_type = 'image/png'; //if constructor, or loadData() is used this filled automatically
 *
 * Upload file :  ApiAttachmentData[blob_data]
 * CUploadedFile::getInstance($this->attachmentData, 'blob_data')
 *
 */
class RequestSaveHandler extends CModel
{
    private $request_obj;

    private $attachment_data = [];

    private $request_routine;

    private $request_queue_value;

    public function __construct()
    {
        $this->request = new Request();
        $this->request_routine = new RequestRoutine();

        $this->request_queue_value = RequestQueue::model()->find()->request_queue;
    }

    /**
     * CModel requires to have this function but it isn't used
     * @return array
     */
    public function attributeNames()
    {
        return [];
    }

    public function attachmentValidator()
    {
        if (empty($this->attachment_data)) {
            $this->addError('attachment_data', 'Attachment data cannot be empty.');
        }
    }

    public function setRequestQueue($value)
    {
        $this->request_queue_value = $value;
    }

    public function sgetRequestQueue($value)
    {
        return $this->request_queue_value;
    }


    /**
     * @inheritdoc
     */
    public function save()
    {
        $transaction = Yii::app()->db->beginTransaction();

        try {
            $error = false;

            if ($this->request->save()) {
                foreach ($this->attachment_data as $attachment_data) {
                    $attachment_data->request_id = $this->request->id;

                    if (!$attachment_data->save()) {
                        $error = true;
                    }
                }
            } else {
                $error = true;
            }

            $error = ($error == false ? !$this->enqueue() : $error);

            if ($error) {
                // add an empty error to flag we have error - just in case if we need to check hasError in this class
                $this->addErrors([]);
                $transaction->rollback();
                return false;
            } else {
                $transaction->commit();
                return $this->request->id;
            }
        } catch (Exception $e) {
            $transaction->rollback();
            \OELog::log($e->getMessage());
            $this->addError('system', $e->getMessage());

            return false;
        }
    }

    private function enqueue()
    {
        $request_routine = RequestRoutine::model()->find([
            'condition' => 'request_id = :request_id',
            'params' => ['request_id' => $this->request->id]
        ]);

        $this->request_routine->request_id = $this->request->id;
        $this->request_routine->execute_request_queue = $this->request->requestType->default_request_queue;
        $this->request_routine->status = "NEW";
        $this->request_routine->routine_name = $this->request->requestType->default_routine_name;
        $this->request_routine->try_count = 0;
        $this->request_routine->execute_sequence = $request_routine ? $request_routine->execute_sequence + 10 : 10;

        return $this->request_routine->save();
    }

    /**
     * Returns the Request model
     * @return model Request
     */
    public function getRequest()
    {
        return $this->request_obj;
    }

    /**
     * Returns the AttachmentData model
     * @return AttachmentData
     */
    public function getAttachmentData()
    {
        return $this->attachment_data;
    }

    public function getRequestRoutine()
    {
        return $this->request_routine;
    }

    /**
     * Sets the Request model to the request_obj variable
     * or
     * sets the attributes of the Request object if the input is an array
     * @param $request Request|array
     */
    public function setRequest($request)
    {
        if ($request instanceof Request) {
            $this->request_obj = $request;
        } elseif (is_array($request)) {
            $this->request_obj->setAttributes($request);
        }
    }

    /**
     * Sets the RequestRoutine model to the request_routine variable
     * or
     * sets the attributes of the RequestRoutine object if the input is an array
     * @param $request_routine RequestRoutine|array
     */
    public function setRequestRoutine($request_routine)
    {
        if ($request_routine instanceof RequestRoutine) {
            $this->request_routine = $request_routine;
        } elseif (is_array($request_routine)) {
            $this->request_routine->setAttributes($request_routine);
        }
    }

    /**
     * Sets the AttachmentData model to the attachment_data variable
     * or
     * sets the attributes of the AttachmentData object if the input is an array
     * @param $attachment_data AttachmentData|array
     */
    public function setAttachmentData($attachment_data)
    {
        if ($attachment_data instanceof AttachmentData) {
            $this->attachment_data = $attachment_data;
        } elseif (is_array($attachment_data)) {
            $this->attachment_data->setAttributes($attachment_data);
        }
    }

    /**
     * Adds additional AttachmentData objects
     * @param $attachment_data
     */
    public function addAttachmentData($attachment_data)
    {
        if ($attachment_data instanceof AttachmentData) {
            $this->attachment_data[] = $attachment_data;
        } elseif (is_array($attachment_data)) {
            $attachment = new AttachmentData();
            $attachment->setAttributes($attachment_data);
            $this->attachment_data[] = $attachment;
        }
    }

    /**
     * @inheritdoc
     */
    public function errorSummary()
    {
        $error_lists = [];
        foreach ($this->getAllModels() as $id => $model) {
            foreach ($model->getErrors() as $attr => $error) {
                // attributes used internally shouldn't be reported to the user
                if (!in_array($attr, ['request_id'])) {
                    $error_lists[$attr] = $error;
                }
            }
        }

        return $error_lists + $this->getErrors();
    }

    /**
     * Returns all the models
     * @return array
     */
    private function getAllModels()
    {
        return array_merge([$this->request, $this->request_routine], $this->attachment_data);
    }
}
