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
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "attachment_data".
 *
 * The followings are the available columns in table 'attachment_data':
 * @property integer $id
 * @property integer $request_id
 * @property string $attachment_mnemonic
 * @property string $body_site_snomed_type
 * @property integer $system_only_managed
 * @property string $attachment_type
 * @property string $mime_type
 * @property string $blob_data
 * @property string $text_data
 * @property string $upload_file_name
 * @property string $thumbnail_small_blob
 * @property string $thumbnail_medium_blob
 * @property string $thumbnail_large_blob
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property AttachmentType $attachmentType
 * @property BodySiteType $bodySiteSnomedType
 * @property MimeType $mimeType
 * @property User $lastModifiedUser
 * @property Request $request
 * @property EventAttachmentItem[] $eventAttachmentItems
 */
class AttachmentData extends BaseActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AttachmentData the static model class
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
        return 'attachment_data';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('request_id, attachment_mnemonic, system_only_managed, attachment_type, mime_type', 'required'),
            array('request_id, system_only_managed', 'numerical', 'integerOnly' => true),
            array('mime_type', 'mimeTypeValidator'),
            array('attachment_type', 'attachmentTypeValidator'),
            array('body_site_snomed_type', 'bodySiteValidator'),
            array('attachment_mnemonic, body_site_snomed_type, attachment_type', 'length', 'max' => 45),
            array('mime_type, upload_file_name', 'length', 'max' => 100),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('blob_data, text_data, thumbnail_small_blob, thumbnail_medium_blob, thumbnail_large_blob, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, request_id, attachment_mnemonic, body_site_snomed_type, system_only_managed, attachment_type, mime_type, blob_data, text_data, upload_file_name, thumbnail_small_blob, thumbnail_medium_blob, thumbnail_large_blob, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on' => 'search'),
        );
    }

    public function dataValidator($attribute, $param)
    {
        if (!$this->text_data && !$this->blob_data && !$this->hasErrors('blob_data')) {
            $this->addError($attribute, 'text_data must be set when blob_data is empty.');
        }
    }

    public function mimeTypeValidator($attribute, $param)
    {
        $mime_type = MimeType::model()->findByPk($this->mime_type);
        if (!$mime_type) {
            $this->addError($attribute, "Uploaded file's mime type is not allowed: " . $this->mime_type);
        }
    }

    public function attachmentTypeValidator($attribute, $param)
    {
        $attachment_type = AttachmentType::model()->findByPk($this->attachment_type);
        if (!$attachment_type) {
            $this->addError($attribute, "Attachment type is not allowed: " . $this->attachment_type);
        }
    }

    public function bodySiteValidator($attribute, $param)
    {
        $body_site_type = BodySiteType::model()->findByPk($this->body_site_snomed_type);
        if ($this->body_site_snomed_type !== null && !$body_site_type) {
            $this->addError($attribute, "Body Site Type is not allowed: " . $this->body_site_type);
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'attachmentType' => array(self::BELONGS_TO, 'AttachmentType', 'attachment_type'),
            'bodySiteSnomedType' => array(self::BELONGS_TO, 'BodySiteType', 'body_site_snomed_type'),
            'mimeType' => array(self::BELONGS_TO, 'MimeType', 'mime_type'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'request' => array(self::BELONGS_TO, 'Request', 'request_id'),
            'eventAttachmentItems' => array(self::HAS_MANY, 'EventAttachmentItem', 'attachment_data_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'request_id' => 'Request',
            'attachment_mnemonic' => 'Attachment Mnemonic',
            'body_site_snomed_type' => 'Body Site Snomed Type',
            'system_only_managed' => 'System Only Managed',
            'attachment_type' => 'Attachment Type',
            'mime_type' => 'Mime Type',
            'blob_data' => 'Blob Data',
            'text_data' => 'Text Data',
            'upload_file_name' => 'Upload File Name',
            'thumbnail_small_blob' => 'Thumbnail Small Blob',
            'thumbnail_medium_blob' => 'Thumbnail Medium Blob',
            'thumbnail_large_blob' => 'Thumbnail Large Blob',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
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
        $criteria->compare('request_id', $this->request_id);
        $criteria->compare('attachment_mnemonic', $this->attachment_mnemonic, true);
        $criteria->compare('body_site_snomed_type', $this->body_site_snomed_type, true);
        $criteria->compare('system_only_managed', $this->system_only_managed);
        $criteria->compare('attachment_type', $this->attachment_type, true);
        $criteria->compare('mime_type', $this->mime_type, true);
        $criteria->compare('blob_data', $this->blob_data, true);
        $criteria->compare('text_data', $this->text_data, true);
        $criteria->compare('upload_file_name', $this->upload_file_name, true);
        $criteria->compare('thumbnail_small_blob', $this->thumbnail_small_blob, true);
        $criteria->compare('thumbnail_medium_blob', $this->thumbnail_medium_blob, true);
        $criteria->compare('thumbnail_large_blob', $this->thumbnail_large_blob, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
