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

use OE\factories\models\traits\HasFactory;

class Element_OphCiExamination_NearVisualAcuity extends Element_OphCiExamination_VisualAcuity
{
    use traits\CustomOrdering;
    use HasFactory;

    protected $relation_defaults = [
        'left_readings' => [
            'side' => OphCiExamination_NearVisualAcuity_Reading::LEFT,
        ],
        'right_readings' => [
            'side' => OphCiExamination_NearVisualAcuity_Reading::RIGHT,
        ],
        'beo_readings' => [
            'side' => OphCiExamination_NearVisualAcuity_Reading::BEO,
        ]
    ];

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
        return [
            'eventType' => [self::BELONGS_TO, 'EventType', 'event_type_id'],
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'eye' => [self::BELONGS_TO, 'Eye', 'eye_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'readings' => [
                self::HAS_MANY,
                OphCiExamination_NearVisualAcuity_Reading::class,
                'element_id'
            ],
            'right_readings' => [
                self::HAS_MANY,
                OphCiExamination_NearVisualAcuity_Reading::class,
                'element_id',
                'on' => 'right_readings.side = ' . OphCiExamination_NearVisualAcuity_Reading::RIGHT
            ],
            'left_readings' => [
                self::HAS_MANY,
                OphCiExamination_NearVisualAcuity_Reading::class,
                'element_id',
                'on' => 'left_readings.side = ' . OphCiExamination_NearVisualAcuity_Reading::LEFT
            ],
            'beo_readings' => [
                self::HAS_MANY,
                OphCiExamination_NearVisualAcuity_Reading::class,
                'element_id',
                'on' => 'beo_readings.side = ' . OphCiExamination_NearVisualAcuity_Reading::BEO
            ],
        ];
    }
}
