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
 * This is the model class for table "request".
 *
 * The followings are the available columns in table 'request':
 * @property integer $id
 * @property string $request_type
 * @property string $system_message
 * @property string $request_override_default_queue
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property AttachmentData[] $attachmentDatas
 * @property User $createdUser
 * @property RequestType $requestType
 * @property User $lastModifiedUser
 * @property RequestQueue $requestOverrideDefaultQueue
 * @property RequestRoutine[] $requestRoutines
 * @property \OEModule\OphGeneric\models\RequestDetails[] $requestDetails
 */
class Request extends BaseActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Request the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'request';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['request_type, system_message', 'required'],
            ['request_type', 'requestTypeValidator'],
            ['request_type, request_override_default_queue', 'length', 'max' => 45],
            ['system_message', 'length', 'max' => 255],
            ['last_modified_user_id, created_user_id', 'length', 'max' => 10],
            ['last_modified_date, created_date', 'safe'],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['id, request_type, system_message, request_override_default_queue, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on' => 'search'],
        ];
    }

    public function requestTypeValidator($attribute, $param)
    {
        $request_type = RequestType::model()->findByPk($this->request_type);
        if (!$request_type) {
            $this->addError($attribute, "Unknown request type: " . $this->request_type);
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'attachmentDatas' => [self::HAS_MANY, 'AttachmentData', 'request_id'],
            'mediaAttachmentData' => [self::HAS_ONE, 'AttachmentData', 'request_id', 'on'=>'attachment_mnemonic = "event_pdf"'],
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'requestType' => [self::BELONGS_TO, 'RequestType', 'request_type'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'requestOverrideDefaultQueue' => [self::BELONGS_TO, 'RequestQueue', 'request_override_default_queue'],
            'requestRoutines' => [self::HAS_MANY, 'RequestRoutine', 'request_id'],
            'requestDetails' => [self::HAS_MANY, '\OEModule\OphGeneric\models\RequestDetails', 'request_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_type' => 'Request Type',
            'system_message' => 'System Message',
            'request_override_default_queue' => 'Request Override Default Queue',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'payload_received' => 'Payload Received',
            'overall_status' => 'Overall Status',
            'steps' => 'Steps',
            'payload_size' => 'Payload Size (count)',
            'attached_size' => 'Attached size (count)',
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('request_type', $this->request_type, true);
        $criteria->compare('system_message', $this->system_message, true);
        $criteria->compare('request_override_default_queue', $this->request_override_default_queue, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }

    public function getRequestStatus()
    {
        $status = "";
        foreach ($this->requestRoutines as $requestRoutine) {
            if ($requestRoutine->status == "FAILED") {
                $status = "FAILED (" . $requestRoutine->routine_name . ") ( $requestRoutine->try_count )";
                break;
            }

            if ($requestRoutine->status == "RETRY" || $requestRoutine->status == "NEW") {
                $status = "RUNNING (" . $requestRoutine->routine_name . ") ( $requestRoutine->try_count )";
                break;
            }
        }

        if ($status === "") {
            $status = "COMPLETE";
        }

        return $status;
    }

    public function getAttachmentsSizeAndCount($attached_only = false)
    {
        $result = 0;
        $count = 0;

        $criteria = new CDbCriteria();

        if ($attached_only) {
            $criteria->join = "JOIN event_attachment_item eai ON t.id=eai.attachment_data_id";
        }

        $criteria->addCondition("request_id=:request_id");
        $criteria->params[':request_id'] = $this->id;
        $attachments = AttachmentData::model()->findAll($criteria);
        foreach ($attachments as $attachment) {
            if ($attachment->blob_data) {
                $result += strlen($attachment->blob_data) / 1000;
            } else {
                $result += strlen($attachment->text_data) / 1000;
            }
            $count++;
        }

        return $result . ' K (' . $count . ')';
    }

    public function getTotalAndCompletedRoutinesDisplay()
    {
        $completed_requests = 0;

        foreach ($this->requestRoutines as $requestRoutine) {
            if ($requestRoutine->status === RequestRoutineStatus::COMPLETE_STATUS) {
                $completed_requests++;
            }
        }

        return $completed_requests . ' of ' . count($this->requestRoutines);
    }
}
