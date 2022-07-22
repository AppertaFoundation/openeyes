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

use OEModule\OphCiExamination\widgets\PupillaryAbnormalities as PupillaryAbnormalitiesWidget;

/**
 * This is the model class for table "et_ophciexamination_pupillary_abnormalities".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property datetime $no_pupillaryabnormalities_date_left
 * @property datetime $no_pupillaryabnormalities_date_right
 *
 * @property PupillaryAbnormalitiesEntry[] $entries
 * @property PupillaryAbnormalitiesEntry[] $entries_left
 * @property PupillaryAbnormalitiesEntry[] $entries_right
 */
class PupillaryAbnormalities extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    protected $auto_update_relations = true;

    protected $widgetClass = PupillaryAbnormalitiesWidget::class;
    protected $default_from_previous = true;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_pupillary_abnormalities';
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
            array('event_id, no_pupillaryabnormalities_date_left, no_pupillaryabnormalities_date_right, entries', 'safe'),
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
            'entries' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\PupillaryAbnormalityEntry', 'element_id',),
            'entries_left' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\PupillaryAbnormalityEntry', 'element_id', 'on' => 'entries_left.eye_id = '.\Eye::LEFT),
            'entries_right' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\PupillaryAbnormalityEntry', 'element_id', 'on' => 'entries_right.eye_id = '.\Eye::RIGHT),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'no_pupillaryabnormalities_date_left' => 'No pupillary abnormalities',
            'no_pupillaryabnormalities_date_right' => 'No pupillary abnormalities',
        );
    }

    /**
     * Get list of available pupillary abnormalities for this element
     */
    public function getAbnormalityOptions()
    {
        $force = array();
        foreach (array('left', 'right') as $side) {
            foreach ($this->{'entries_'.$side} as $entry) {
                $force[] = $entry->abnormality_id;
            }
        }

        return OphCiExamination_PupillaryAbnormalities_Abnormality::model()->activeOrPk($force)->findAll();
    }

    public function beforeSave()
    {
        foreach (array('left', 'right') as $side) {
            if ($this->{'no_pupillaryabnormalities_date_' . $side}) {
                $entries = $this->{'entries_' . $side};
                foreach ($entries as $key => $entry) {
                    if ($entry->has_abnormality === PupillaryAbnormalityEntry::$NOT_CHECKED) {
                        unset($entries[$key]);
                    }
                }
                $this->{'entries_'.$side} = $entries;
            }
        }
        return parent::beforeSave();
    }

    /**
     * Check for auditable changes
     *
     */
    protected function checkForAudits()
    {
        foreach (array('left', 'right') as $side) {
            if (!$this->eyeHasSide($side, $this->eye_id)) {
                continue;
            }

            if ($this->isAttributeDirty('no_pupillaryabnormalities_date_' . $side)) {
                if ($this->{'no_pupillaryabnormalities_date_' . $side}) {
                    $this->addAudit('set-nopupillaryabnormalitiesdate_' . $side);
                } else {
                    $this->addAudit('remove-nopupillaryabnormalitiesdate_' . $side);
                }
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
        foreach (array('left', 'right') as $side) {
            if (!$this->eyeHasSide($side, $element->eye_id)) {
                continue;
            }

            $this->{'no_pupillaryabnormalities_date_' . $side} = $element->{'no_pupillaryabnormalities_date_' . $side};

            // use previous session's entries
            $entries = $this->{'entries_' . $side};
            // if there are no posted entries from previous session
            if (!$entries) {
                // add the entries from the DB
                foreach ($element->{'entries_' . $side} as $entry) {
                    $new_entry = new PupillaryAbnormalityEntry();
                    $new_entry->loadFromExisting($entry);
                    if ($this->{'no_pupillaryabnormalities_date_' . $side} != null) {
                        $new_entry->has_abnormality = (string)PupillaryAbnormalityEntry::$NOT_PRESENT;
                    }
                    $entries[] = $new_entry;
                }
            }
            $this->{'entries_' . $side} = $entries;
        }
        $this->originalAttributes = $this->getAttributes();
        $this->is_initialized = true;
    }

    public function getSortedEntries($eye_side)
    {
        return $this->sortEntries($this->{'entries_' . $eye_side});
    }

    /**
     * Returns sorted PupillaryAbnormalityEntries
     * @param $entries
     * @return mixed
     */
    private function sortEntries($entries)
    {
        usort($entries, function ($a, $b) {
            if ($a->has_abnormality === $b->has_abnormality) {
                return 0;
            }
            return $a->has_abnormality < $b->has_abnormality ? 1 : -1;
        });

        return $entries;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        foreach (array('left', 'right') as $side) {
            if (!$this->eyeHasSide($side, $this->eye_id)) {
                continue;
            }

            if ($this->{'no_pupillaryabnormalities_date_' . $side}) {
                return 'Patient has no known pupillary abnormalities.';
            } else {
                $entries = $this->sortEntries($this->{'entries_' . $side});
                return implode(' <br /> ', $entries);
            }
        }
    }

    /**
     * check either confirmation of no pupillary abnormalities or at least one pupillary abnormality entry
     */
    public function afterValidate()
    {
        $hasLeft = false;
        $hasRight = false;

        foreach ($this->entries as $i => $entry) {
            if ($entry->eye_id == \Eye::LEFT && $hasLeft === false) {
                $hasLeft = true;
                continue;
            }
            if ($entry->eye_id == \Eye::RIGHT && $hasRight === false) {
                $hasRight = true;
            }
        }

        if (!$hasLeft && !$this->no_pupillaryabnormalities_date_left) {
            $this->addError('left', 'Left side has no data.');
        }
        if (!$hasRight && !$this->no_pupillaryabnormalities_date_right) {
            $this->addError('right', 'Right side has no data.');
        }

        parent::afterValidate();
    }
}
