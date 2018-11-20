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

namespace OEModule\OphCiExamination\models;

class Element_OphCiExamination_NearVisualAcuity extends Element_OphCiExamination_VisualAcuity
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_nearvisualacuity';
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'unit' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit', 'unit_id', 'on' => 'unit.is_near = 1'),
            'readings' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_NearVisualAcuity_Reading', 'element_id'),
            'right_readings' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_NearVisualAcuity_Reading', 'element_id', 'on' => 'right_readings.side = '.OphCiExamination_VisualAcuity_Reading::RIGHT),
            'left_readings' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_NearVisualAcuity_Reading', 'element_id', 'on' => 'left_readings.side = '.OphCiExamination_VisualAcuity_Reading::LEFT),
        );
    }

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
     * returns the default letter string for the va readings. Converts all readings to Snellen Metre
     * as this is assumed to be the standard for correspondence.
     *
     * @TODO: The units for correspondence should become a configuration variable
     *
     * @throws Exception
     *
     * @return string
     */
    public function getLetter_string()
    {
        if (!$unit = OphCiExamination_VisualAcuityUnit::model()->find('name = ?', array(Yii::app()->params['ophciexamination_visualacuity_correspondence_unit']))) {
            throw new Exception('Configured visual acuity correspondence unit was not found: '.Yii::app()->params['ophciexamination_visualacuity_correspondence_unit']);
        }

        $text = "Near Visual acuity:\n";

        if ($this->hasRight()) {
            $text .= 'Right Eye: ';
            if ($this->getCombined('right')) {
                $text .= $this->getCombined('right', $unit->id);
            } else {
                $text .= $this->getTextForSide('right');
            }
        } else {
            $text .= 'Right Eye: not recorded';
        }
        $text .= "\n";

        if ($this->hasLeft()) {
            $text .= 'Left Eye: ';
            if ($this->getCombined('left')) {
                $text .= $this->getCombined('left', $unit->id);
            } else {
                $text .= $this->getTextForSide('left');
            }
        } else {
            $text .= 'Left Eye: not recorded';
        }

        return $text."\n";
    }

    public function setDefaultOptions(\Patient $patient = null)
    {
        $this->unit_id = $this->getSetting('unit_id');
        if ($rows = $this->getSetting('default_rows')) {
            $left_readings = array();
            $right_readings = array();
            for ($i = 0; $i < $rows; ++$i) {
                $left_readings[] = new OphCiExamination_NearVisualAcuity_Reading();
                $right_readings[] = new OphCiExamination_NearVisualAcuity_Reading();
            }
            $this->left_readings = $left_readings;
            $this->right_readings = $right_readings;
        }
    }

    public function afterSave()
    {
        foreach (array('left', 'right') as $eye_side) {
            if ($this->{$eye_side .'_unable_to_assess'} || $this->{$eye_side .'_eye_missing'}) {
                foreach ($this->{$eye_side .'_readings'} as $reading) {
                    $reading->delete();
                }
            }
        }
        parent::afterSave();
    }
}
