<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;


/**
 * Class Allergies
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $event_id
 * @property datetime $no_allergies_date
 *
 * @property \Event $event
 * @property Allergy_Entry[] $entries
 * @property \EventType $eventType
 * @property \User $user
 * @property \User $usermodified
 */
class Allergies extends \BaseEventTypeElement
{
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    public $widgetClass = 'OEModule\OphCiExamination\widgets\Allergies';
    protected $default_from_previous = true;

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
            $this->addError('no_allergies_date', 'Please confirm the patient has no allergies.');
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
     * @param \BaseEventTypeElement $element
     */
    public function loadFromExisting($element)
    {
        $this->no_allergies_date = $element->no_allergies_date;
        $entries = array();
        foreach ($element->entries as $entry) {
            $new = new AllergyEntry();
            $new->loadFromExisting($entry);
            $entries[] = $new;
        }
        $this->entries = $entries;
        $this->originalAttributes = $this->getAttributes();
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

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->no_allergies_date) {
            return 'Patient has no known allergies';
        } else {
            return implode(' // ', $this->entries);
        }
    }

}
