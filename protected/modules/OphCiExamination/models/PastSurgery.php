<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;


/**
 * Class PastSurgery
 *
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $event_id
 *
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property PastSurgery_Operation[] $operations
 * @property string $comments
 */
class PastSurgery extends \BaseEventTypeElement
{

    protected $auto_update_relations = true;
    public $widgetClass = 'OEModule\OphCiExamination\widgets\PastSurgery';
    protected $default_from_previous = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'et_ophciexamination_pastsurgery';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array(
            'PatientLevelElementBehaviour' => 'PatientLevelElementBehaviour',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, operations, comments', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, comments',  'safe', 'on' => 'search')
        );
    }

    /**
     * @return array
     */
    public function relations()
    {
        return array(
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'operations' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\PastSurgery_Operation', 'element_id', 'order' => 'operations.date desc, operations.last_modified_date'),
        );
    }

    public function beforeSave()
    {
        $entries = $this->operations;
        foreach ($entries as $key=>$entry) {
            if($entry->had_operation == PastSurgery_Operation::$NOT_CHECKED) {
                unset($entries[$key]);
            }
        }
        $this->operations = $entries;
        return parent::beforeSave();
    }

    /**
     * individual operation validation
     */
    public function afterValidate()
    {
        foreach ($this->operations as $i => $operation) {
            if (!$operation->validate()) {
                foreach ($operation->getErrors() as $fld => $err) {
                    $this->addError('operations_' . ($i), 'Operation ('.($i + 1).'): '.implode(', ', $err));
                }
            }
        }
        parent::afterValidate();
    }


    /**
     * Will duplicate values from the current socialhistory property of the given patient.
     *
     * @param static $element
     */
    public function loadFromExisting($element)
    {
        // use previous session's entries
        $operations = $this->operations;

        // if there are no posted entries from previous session
        if (!$operations) {
            // add the entries from the DB
            foreach ($element->operations as $prev) {
                $operation = new PastSurgery_Operation();
                $operation->operation = $prev->operation;
                $operation->side_id = $prev->side_id;
                $operation->date = $prev->date;
                $operation->had_operation = $prev->had_operation;
                $operations[] = $operation;
            }
        }

        $this->operations = $operations;
        $this->comments = $element->comments;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' <br /> ', $this->operations);
    }

    public function getTileSize($action)
    {
        return $action === 'view' || $action === 'createImage' ? 1 : null;
    }
    
    public function getDisplayOrder($action)
    {
        if ($action=='view'){
            return 10;
        }
        else{
            return parent::getDisplayOrder($action);
        }
    }
}