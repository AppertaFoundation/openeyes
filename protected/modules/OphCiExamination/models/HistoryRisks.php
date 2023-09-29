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

use OE\factories\models\traits\HasFactory;
use OEModule\OphCiExamination\widgets\HistoryRisks as HistoryRisksWidget;

/**
 * Class HistoryRisks
 *
 * @package OEModule\OphCiExamination\models
 */
class HistoryRisks extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    use HasFactory;
    protected $default_view_order = 55;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    protected $widgetClass = HistoryRisksWidget::class;
    protected $default_from_previous = true;
    protected $errorExceptions = array(
        'OEModule_OphCiExamination_models_HistoryRisks_no_risks_date' => 'OEModule_OphCiExamination_models_HistoryRisks_no_risks',
        'OEModule_OphCiExamination_models_HistoryRisks_entries' => 'OEModule_OphCiExamination_models_HistoryRisks_entry_table'

    );

    public function tableName()
    {
        return 'et_ophciexamination_history_risks';
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
                'OEModule\OphCiExamination\models\HistoryRisksEntry',
                'element_id',
            ),
            'not_checked' => array(
                self::HAS_MANY,
                'OEModule\OphCiExamination\models\HistoryRisksEntry',
                'element_id',
                'condition' => 'has_risk = -9'
            ),
            'present' => array(
                self::HAS_MANY,
                'OEModule\OphCiExamination\models\HistoryRisksEntry',
                'element_id',
                'condition' => 'has_risk = 1'
            ),
            'not_present' => array(
                self::HAS_MANY,
                'OEModule\OphCiExamination\models\HistoryRisksEntry',
                'element_id',
                'condition' => 'has_risk = 0'
            )
        );
    }

    /**
     * @param HistoryRisks $element
     */
    public function loadFromExisting($element)
    {
        // use previous session's entries
        $entries = $this->entries;

        // if there are no posted entries from previous session
        if (!$entries) {
            // add the entries from the DB
            foreach ($element->entries as $entry) {
                $new_entry = new HistoryRisksEntry();
                $new_entry->loadFromExisting($entry);
                $entries[] = $new_entry;
            }
        }

        $this->entries = $entries;
        $this->originalAttributes = $this->getAttributes();
    }

    private $required_risks = null;

    /**
     * Various checks against the entries assigned to the model
     *
     * @inheritdoc
     */
    public function afterValidate()
    {
        if (!$this->no_risks_date && !$this->entries && $this->getScenario() !== 'auto') {
            $this->addError('no_risks_date', 'Please confirm the patient has no risks.');
        }
        $risk_ids = array();

        // prevent duplicate entries
        foreach ($this->entries as $entry) {
            if ($entry->risk) {
                if (!$entry->risk->isOther() && in_array($entry->risk_id, $risk_ids)) {
                    $this->addError('entries', 'Cannot have duplicate entry for ' . $entry->risk);
                } else {
                    $risk_ids[] = $entry->risk_id;
                }
            }
        }

        parent::afterValidate();
    }

    /**
     * @return bool
     *
     * If a risk is "not checked", do not store in db
     */

    /**
     * Get list of available risks for this element (ignoring required risks)
     */
    public function getRiskOptions()
    {
        $force = array();
        foreach ($this->entries as $entry) {
            $force[] = $entry->risk_id;
        }
        return OphCiExaminationRisk::model()->activeOrPk($force)->findAll();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $result = array();
        foreach (array('present', 'not_checked', 'not_present') as $cat) {
            $result[] = $this->getAttributeLabel($cat) . ': ' . implode(', ', $this->$cat);
        }
        return implode(' <br /> ', $result);
    }

    /**
     * @param string $category
     * @return string
     */
    public function getEntriesDisplay($category = 'entries')
    {
        if (!in_array($category, array('present', 'not_checked', 'not_present'))) {
            $category  = 'entries';
        }
        return  array_map(function($e) { return $e->getDisplay();
        }, $this->$category);
    }

    public function getSortedEntries()
    {
        return $this->sortEntries($this->entries);
    }

    private function sortEntries($entries)
    {
        usort($entries, function ($a, $b) {
            if ($a->has_risk == $b->has_risk) {
                return 0;
            }
            return $a->has_risk < $b->has_risk ? 1 : -1;
        });

        return $entries;
    }

    public function getHistoryRisksEntries()
    {
        $entries = [];
        foreach ($this->getHistoryRisksEntryKeys() as $key) {
            $entries[$key] = array_values(array_filter($this->getSortedEntries(), function ($e) use ($key) {
                return $e->has_risk === $key;
            }));
        }
        return $entries;
    }

    public function getHistoryRisksEntryKeys()
    {
        return array(
            HistoryRisksEntry::$PRESENT => "1",
            HistoryRisksEntry::$NOT_PRESENT => "0",
            HistoryRisksEntry::$NOT_CHECKED => "-9"
        );
    }

    /**
     * @param $attribute
     * @inheritdoc
     */
    protected function errorAttributeException($attribute, $message)
    {
        if ($attribute === \CHtml::modelName($this) . '_entries') {
            // TODO: handle highlighting the "other" text field once that validation is in place.
            if (preg_match('/^(\d+)/', $message, $match) === 1) {
                return $attribute .'_' . ($match[1]-1) . '_risk_id';
            }
        }
        return parent::errorAttributeException($attribute, $message);
    }

    /**
     * @param $name
     * @return HistoryRisksEntry|null
     */
    public function getRiskEntryByName($name)
    {
        foreach ($this->entries as $entry) {
            if ($entry->risk->name === $name) {
                return $entry;
            }
        }
        return null;
    }
}
