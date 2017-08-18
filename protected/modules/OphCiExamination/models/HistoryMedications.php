<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * Class HistoryMedications
 * @package OEModule\OphCiExamination\models
 *
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property HistoryMedicationsEntry[] $entries
 */
class HistoryMedications extends \BaseEventTypeElement
{
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    public $widgetClass = 'OEModule\OphCiExamination\widgets\HistoryMedications';
    protected $default_from_previous = true;

    public function tableName()
    {
        return 'et_ophciexamination_history_medications';
    }

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
            array('event_id, entries', 'safe'),
            array('entries', 'required', 'message' => 'At least one medication must be recorded.')
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
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'entries' => array(
                self::HAS_MANY,
                'OEModule\OphCiExamination\models\HistoryMedicationsEntry',
                'element_id',
            )
        );
    }

    protected function errorAttributeException($attribute, $message)
    {
        if ($attribute === \CHtml::modelName($this) . '_entries') {
            if (preg_match('/^(\d+)/', $message, $match) === 1) {
                return \CHtml::modelName($this) . '_entry_table tbody tr:eq(' . ($match[1]-1) . ')';
            }
        }
        return parent::errorAttributeException($attribute, $message);
    }

    /**
     * @param HistoryMedications $element
     */
    public function loadFromExisting($element)
    {
        $entries = array();
        foreach ($element->entries as $entry) {
            $new = new HistoryMedicationsEntry();
            $new->loadFromExisting($entry);
            $entries[] = $new;
        }
        $this->entries = $entries;
        $this->originalAttributes = $this->getAttributes();
    }

    /**
     * @return \DrugRoute[]
     */
    public function getRouteOptions()
    {
        $force = array();
        foreach ($this->entries as $entry) {
            $force[] = $entry->route_id;
        }
        return \DrugRoute::model()->activeOrPk($force)->findAll();
    }

    /**
     * @return \DrugFrequency[]
     */
    public function getFrequencyOptions()
    {
        $force = array();
        foreach ($this->entries as $entry) {
            $force[] = $entry->frequency_id;
        }

        return \DrugFrequency::model()->activeOrPk($force)->findAll();
    }
}