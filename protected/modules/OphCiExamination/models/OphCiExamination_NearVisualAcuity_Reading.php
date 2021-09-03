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

use OEModule\OphCiExamination\models\traits\HasRelationOptions;

class OphCiExamination_NearVisualAcuity_Reading extends OphCiExamination_VisualAcuity_Reading
{
    protected static $complex_relations = ["source", "occluder"];

    public function tableName()
    {
        return 'ophciexamination_nearvisualacuity_reading';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'element' => [self::BELONGS_TO, Element_OphCiExamination_NearVisualAcuity::class, 'element_id'],
            'method' => [self::BELONGS_TO, OphCiExamination_VisualAcuity_Method::class, 'method_id'],
            'unit' => [self::BELONGS_TO, OphCiExamination_VisualAcuityUnit::class, 'unit_id'],
            'source' => [self::BELONGS_TO, OphCiExamination_VisualAcuitySource::class, 'source_id'],
            'occluder' => [self::BELONGS_TO, OphCiExamination_VisualAcuityOccluder::class, 'occluder_id']
        ];
    }

    public function sourceOptions()
    {
        $current_pks = $this->source_id ? [$this->source_id] : [];
        $cache_key = "near-" . self::getRelationOptionsCacheKey(
            OphCiExamination_VisualAcuitySource::class,
            $current_pks
        );

        return self::getAndSetRelationOptionsCache(
            $cache_key,
            function () use ($current_pks) {
                return OphCiExamination_VisualAcuitySource::model()
                    ->activeOrPk($current_pks)
                    ->findAll(
                        [
                            'condition' => 'is_near = 1',
                            'order' => 'display_order asc'
                        ]
                    );
            }
        );
    }
}
