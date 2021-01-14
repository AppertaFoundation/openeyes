<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


namespace OEModule\OphCiExamination\models;


use OEModule\OphCiExamination\widgets\FamilyHistory as FamilyHistoryWidget;

/**
 * Class FamilyHistory
 *
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $event_id
 * @property datetime $no_family_history_date
 *
 * @property \Event $event
 * @property FamilyHistory_Entry[] $entries
 * @property \EventType $eventType
 * @property \User $user
 * @property \User $usermodified
 */
class FamilyHistory extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    protected $auto_update_relations = true;
    protected $widgetClass = FamilyHistoryWidget::class;
    protected $default_from_previous = true;
    protected $errorExceptions = array(
        'OEModule_OphCiExamination_models_FamilyHistory_no_family_history_date' => 'OEModule_OphCiExamination_models_FamilyHistory_no_family_history'
    );

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
        return 'et_ophciexamination_familyhistory';
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
            array('event_id, no_family_history_date, entries', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id', 'safe', 'on' => 'search'),
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
            'entries' => array(
                self::HAS_MANY,
                'OEModule\OphCiExamination\models\FamilyHistory_Entry',
                'element_id',
            ),
        );
    }

    public function afterValidate()
    {
        if (!$this->no_family_history_date && !$this->entries) {
            $this->addError('no_family_history', 'Please confirm there are no family history entries to be recorded.');
        }

        foreach ($this->entries as $i => $entry) {
            if (!$entry->validate()) {
                foreach ($entry->getErrors() as $fld => $err) {
                    $this->addError('entries', 'History Entry ('.($i + 1).'): '.implode(', ', $err));
                }
            }
        }

        return parent::afterValidate();
    }

    /**
     * @param FamilyHistory $element
     * @inheritdoc
     */
    public function loadFromExisting($element)
    {
        // use previous session's entries
        $entries = $this->entries;
        $this->no_family_history_date = $element->no_family_history_date;

        // if there are no posted entries from previous session
        if (!$entries) {
            // add the entries from the DB
            foreach ($element->entries as $entry) {
                $new_entry = new FamilyHistory_Entry();
                $new_entry->loadFromExisting($entry);
                $entries[] = $new_entry;
            }
        }

        $this->entries = $entries;
    }

    /**
     * @return FamilyHistoryRelative[]
     */
    public function getRelativeOptions()
    {
        $force = array();
        foreach ($this->entries as $entry) {
            $force[] = $entry->relative_id;
        }
        return FamilyHistoryRelative::model()->activeOrPk($force)->findAll();
    }

    /**
     * @return FamilyHistoryCondition[]
     */
    public function getConditionOptions()
    {
        $force = array();
        foreach ($this->entries as $entry) {
            $force[] = $entry->condition_id;
        }
        return FamilyHistoryCondition::model()->activeOrPk($force)->findAll();
    }

    /**
     * @return FamilyHistorySide[]
     */
    public function getSideOptions()
    {
        $force = array();
        foreach ($this->entries as $entry) {
            $force[] = $entry->relative_id;
        }
        return FamilyHistorySide::model()->activeOrPk($force)->findAll();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->no_family_history_date) {
            return 'Patient has no known family history';
        } else {
            return implode(' <br /> ', $this->entries);
        }
    }
}
