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

/**
 * This is the model class for table "element_procedurelist".
 *
 * The followings are the available columns in table 'element_operation':
 *
 * @property string $id
 * @property int $event_id
 * @property int $surgeon_id
 * @property int $assistant_id
 * @property int $anaesthetic_type
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class Element_OphTrOperationnote_SiteTheatre extends Element_OpNote
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return ElementOperation the static model class
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
        return 'et_ophtroperationnote_site_theatre';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, site_id, theatre_id', 'safe'),
            array('site_id', 'required'),
            array('theatre_id', 'required', 'on' => array('insert')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'site_id' => 'Site',
            'theatre_id' => 'Theatre',
        );
    }
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'theatre' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Theatre', 'theatre_id'),
        );
    }

    /**
     * Set default values for forms on create.
     * @param Patient $patient
     */
    public function setDefaultOptions(Patient $patient = null)
    {
        if (Yii::app()->controller->getBookingOperation()) {
            $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
            $event = Event::model()->findByPk(Yii::app()->controller->getBookingOperation()->event_id);

            if (!$this->site_id) {
                $site = $api->findSiteForBookingEvent($event);
                if ($site) {
                    $this->site_id = $site->id;
                } elseif (isset(Yii::app()->controller->getBookingOperation()->site_id)) {
                    $this->site_id = Yii::app()->controller->getBookingOperation()->site_id;
                }
            }
            if (!$this->theatre_id) {
                $theatre = $api->findTheatreForBookingEvent($event);
                if ($theatre) {
                    $this->theatre_id = $theatre->id;
                }
            }
        } else {
            if (!$this->site_id) {
                $this->site_id = Yii::app()->session['selected_site_id'];
            }
        }
    }

    public function getDefaultFormOptions(array $context): array
    {
        $fields = array();
        if (Yii::app()->controller->getBookingOperation()) {
            $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
            $event = Event::model()->findByPk($context['booking']->event_id);

            if (!$this->site_id) {
                $site = $api->findSiteForBookingEvent($event);
                if ($site) {
                    $fields['site_id'] = $site->id;
                } elseif (isset($context['booking']->site_id)) {
                    $fields['site_id'] = $context['booking']->site_id;
                }
            }
            if (!$this->theatre_id) {
                $theatre = $api->findTheatreForBookingEvent($event);
                if ($theatre) {
                    $fields['theatre_id'] = $theatre->id;
                }
            }
        } else {
            if (!$this->site_id) {
                $fields['site_id'] = Yii::app()->session['selected_site_id'];
            }
        }
        return $fields;
    }

    public function getTileSize($action)
    {
        $action_list = array('view', 'createImage', 'removed');
        return in_array($action, $action_list) ? 1 : null;
    }

    public function getFormTItle()
    {
        return 'Location';
    }
}
