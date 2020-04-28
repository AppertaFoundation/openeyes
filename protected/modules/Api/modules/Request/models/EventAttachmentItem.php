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
 * This is the model class for table "event_attachment_item".
 *
 * The followings are the available columns in table 'event_attachment_item':
 * @property integer $id
 * @property integer $event_attachment_group_id
 * @property integer $attachment_data_id
 * @property integer $system_only_managed
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property AttachmentData $attachmentData
 * @property EventAttachmentGroup $eventAttachmentGroup
 * @property User $lastModifiedUser
 */
class EventAttachmentItem extends BaseActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EventAttachmentItem the static model class
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
        return 'event_attachment_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['id, event_attachment_group_id, attachment_data_id, system_only_managed', 'required'],
            ['id, event_attachment_group_id, attachment_data_id, system_only_managed', 'numerical', 'integerOnly' => true],
            ['last_modified_user_id, created_user_id', 'length', 'max' => 10],
            ['last_modified_date, created_date, event_document_view_set', 'safe'],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['id, event_attachment_group_id, attachment_data_id, system_only_managed, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'attachmentData' => [self::BELONGS_TO, 'AttachmentData', 'attachment_data_id'],
            'eventAttachmentGroup' => [self::BELONGS_TO, 'EventAttachmentGroup', 'event_attachment_group_id'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_attachment_group_id' => 'Event Attachment Group',
            'attachment_data_id' => 'Attachment Data',
            'system_only_managed' => 'System Only Managed',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
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
        $criteria->compare('event_attachment_group_id', $this->event_attachment_group_id);
        $criteria->compare('attachment_data_id', $this->attachment_data_id);
        $criteria->compare('system_only_managed', $this->system_only_managed);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }
}
