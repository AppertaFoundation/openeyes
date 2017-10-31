<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\models;

class QueueSet extends \BaseActiveRecordVersioned
{
    public $auto_update_relations = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphTrOperationnote_GlaucomaTube_PlatePosition the static model class
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
        return 'patientticketing_queueset';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, description, category_id, summary_link, allow_null_priority, permissioned_users, default_queue_id, filter_priority, filter_subspecialty, filter_firm, filter_my_tickets, filter_closed_tickets', 'safe'),
            array('name, category_id', 'required'),
            array('initial_queue_id', 'required', 'except' => 'formCreate'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'queuesetcategory' => array(self::BELONGS_TO, 'OEModule\PatientTicketing\models\QueueSetCategory', 'category_id'),
            'initial_queue' => array(self::BELONGS_TO, 'OEModule\PatientTicketing\models\Queue', 'initial_queue_id'),
            'permissioned_users' => array(self::MANY_MANY, 'User', 'patientticketing_queuesetuser(queueset_id, user_id)'),
            'default_queue' => array(self::BELONGS_TO, 'OEModule\PatientTicketing\models\Queue', 'default_queue_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'category_id' => 'Ticket Category',
            'summary_link' => 'Link Tickets to Episode Summary',
            'default_queue_id' => 'Default queue',
            'filter_my_tickets' => 'Filter My Patients',
            'filter_closed_tickets' => 'Filter Completed Patients',
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('category_id', $this->category_id, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }
}
