<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OE\factories\models\traits\HasFactory;
use OEModule\OphCiExamination\widgets\Allergies as AllergiesWidget;

/**
 * Class Allergies
 *
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $event_id
 * @property datetime $no_allergies_date
 *
 * @property \Event $event
 * @property AllergyEntry[] $entries
 * @property \EventType $eventType
 * @property \User $user
 * @property \User $usermodified
 */
class Allergies extends \BaseEventTypeElement
{
    use HasFactory;
    use traits\CustomOrdering;

    protected $default_view_order = 50;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    protected $widgetClass = AllergiesWidget::class;
    protected $default_from_previous = true;

    protected $errorExceptions = array(
        'OEModule_OphCiExamination_models_Allergies_entries' => 'OEModule_OphCiExamination_models_Allergies_entry_table'
    );

    public function tableName()
    {
        return 'et_ophciexamination_allergies';
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
            array('event_id, no_allergies_date, entries', 'safe'),
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
                'OEModule\OphCiExamination\models\AllergyEntry',
                'element_id',
            ),
        );
    }

    /**
     * check either confirmation of no allergies or at least one allergy entry
     */
    public function afterValidate()
    {
        if (!$this->no_allergies_date && !$this->entries) {
            $this->addError('no_allergies', 'Please confirm the patient has no allergies.');
        }
        return parent::afterValidate();
    }


    /**
     * Check for auditable changes
     *
     */
    protected function checkForAudits()
    {

        if ($this->isAttributeDirty('no_allergies_date')) {
            if ($this->no_allergies_date) {
                $this->addAudit('set-noallergydate');
            } else {
                $this->addAudit('remove-noallergydate');
            }
        }
        return parent::checkForAudits();
    }

    protected function doAudit()
    {
        if ($this->isAtTip()) {
            parent::doAudit();
        }
    }

    /**
     * Gets all required missing allergies
     * @return array
     */
    public function getMissingRequiredAllergies($patient)
    {
        $current_ids = array_map(function ($e) {
            return $e->allergy_id;
        },
            $this->entries);

        $missing = array();
        foreach ($this->getRequiredAllergies($patient) as $required) {
            if (!in_array($required->id, $current_ids)) {
                $entry = new AllergyEntry();
                $entry->allergy_id = $required->id;
                $missing[] = $entry;
            }
        }
        return $missing;
    }

    public function getRequiredAllergies($patient)
    {
        $exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
        return $exam_api->getRequiredAllergies($patient);
    }


    /**
     * @param \BaseEventTypeElement $element
     */
    public function loadFromExisting($element)
    {
        $this->no_allergies_date = $element->no_allergies_date;

        // use previous session's entries
        $entries = $this->entries;

        // if there are no posted entries from previous session
        if (!$entries) {
            // add the entries from the DB
            foreach ($element->entries as $entry) {
                $new_entry = new AllergyEntry();

                $new_entry->allergy_id = $entry->allergy_id;
                $new_entry->reactions = $entry->reactions;
                $new_entry->other = $entry->other;
                $new_entry->comments = $entry->comments;
                $new_entry->has_allergy = $entry->has_allergy;

                $entries[] = $new_entry;
            }
        }
        $this->entries = $entries;
        $this->originalAttributes = $this->getAttributes();

        if (isset($element->event_id)) {
            $missing_required_allergies = $this->getMissingRequiredAllergies($element->event->patient);
            if ($missing_required_allergies) {
                $this->no_allergies_date = null;
            } else {
                $this->no_allergies_date = $element->no_allergies_date;
            }
        }
        $this->is_initialized = true;
    }

    /**
     * Get list of available allergies for this element
     */
    public function getAllergyOptions()
    {
        $force = array();
        foreach ($this->entries as $entry) {
            $force[] = $entry->allergy_id;
        }
        return OphCiExaminationAllergy::model()->activeOrPk($force)->findAll();
    }

    protected function errorAttributeException($attribute, $message)
    {
        if ($attribute === \CHtml::modelName($this) . '_entries') {
            if (preg_match('/^(\d+)\s\-\sChecked\sStatus/', $message, $match) === 1) {
                return $attribute .'_' . ($match[1]-1) . '_allergy_has_allergy';
            } elseif (preg_match('/^(\d+)\s\-\sAllergy/', $message, $match) === 1) {
                return $attribute .'_' . ($match[1]-1) . '_allergy_id';
            }
        }
        return parent::errorAttributeException($attribute, $message);
    }

    public function getSortedEntries()
    {
        return $this->sortEntries($this->entries);
    }

    /**
     * Returns sorted AllergyEntries
     * @param $entries
     * @return mixed
     */
    private function sortEntries($entries)
    {
        usort($entries, function ($a, $b) {
            if ($a->has_allergy == $b->has_allergy) {
                return 0;
            }
            return $a->has_allergy < $b->has_allergy ? 1 : -1;
        });

        return $entries;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->no_allergies_date) {
            return 'Patient has no known allergies';
        } else {
            $entries = $this->sortEntries($this->entries);
            return implode(' <br /> ', $entries);
        }
    }

    public function softDelete()
    {
        $this->updateAll(array('deleted' => 1), 'event_id = :event_id', array(':event_id' => $this->event_id));
    }

    public function getAllergyEntryByName($name)
    {
        foreach ($this->entries as $entry) {
            if ($entry->allergy->name === $name) {
                return $entry;
            }
        }
        return null;
    }
}
