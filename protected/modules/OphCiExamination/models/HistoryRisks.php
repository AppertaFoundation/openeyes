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
 * Class HistoryRisks
 * @package OEModule\OphCiExamination\models
 */
class HistoryRisks extends \BaseEventTypeElement
{
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    public $widgetClass = 'OEModule\OphCiExamination\widgets\HistoryRisks';
    protected $default_from_previous = true;

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
                'condition' => 'has_risk is null'
            ),
            'present' => array(
                self::HAS_MANY,
                'OEModule\OphCiExamination\models\HistoryRisksEntry',
                'element_id',
                'condition' => 'has_risk = true'
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
        $entries = array();
        foreach ($element->entries as $entry) {
            $new = new HistoryRisksEntry();
            $new->loadFromExisting($entry);
            $entries[] = $new;
        }
        $this->entries = $entries;
        $this->originalAttributes = $this->getAttributes();
    }

    /**
     * Get list of available allergies for this element
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
        return implode(' // ', $result);
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
        return implode(', ', array_map(function($e) { return $e->getDisplay(); }, $this->$category));
    }


    /**
     * @param $attribute
     * @inheritdoc
     */
    protected function errorAttributeException($attribute, $message)
    {
        if ($attribute === \CHtml::modelName($this) . '_entries') {
            // handle highlighting the "other" text field once that validation is in place.
            if (preg_match('/^(\d+)/', $message, $match) === 1) {
                $attribute .= '_' . ($match[1]-1) . '_risk_id';
            }
        }
        return $attribute;
    }
}