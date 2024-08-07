<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\models;

/**
 * This is the model class for table "patientticketing_ticketqueue_assignment". THis is the link table between tickets and queues.
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property string $widget_id
 * @property int $ticket_id
 * @property int $queue_id
 * @property int $user_id
 */
class UserWidgetAssignment extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return UserWidgetAssignment the static model class
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
        return 'patientticketing_user_widget_assignment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('ticket_id, queue_id, widget_id, user_id', 'required'),
                array('ticket_id, queue_id, widget_id, user_id', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'ticketUser' => array(self::BELONGS_TO, 'User', 'user_id'),
                'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'userModified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'ticket' => array(self::BELONGS_TO, 'OEModule\PatientTicketing\models\Ticket', 'ticket_id'),
                'queue' => array(self::BELONGS_TO, 'OEModule\PatientTicketing\models\Queue', 'queue_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'User' => 'User',
            'widget_id' => 'Id'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }
}
