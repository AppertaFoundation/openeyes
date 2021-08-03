<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models\traits;


use OEModule\OphCiExamination\models\CorrectionType;

/**
 * Trait HasCorrectionType
 *
 * This trait should be used by any models wanting to track a record of a correction type
 * It defines the basic rules and relations for attributes defined to point to that model
 *
 * @package OEModule\OphCiExamination\models\traits
 * @see CorrectionType
 */
trait HasCorrectionType
{

    /**
     * Wrapper for retrieving the correction type attributes consistently
     *
     * @return array
     */
    protected function getCorrectionTypeAttributes(): array
    {
        if (!$this->correction_type_attributes) {
            throw new \RuntimeException('No Correction Type attributes have been configured');
        }

        return $this->correction_type_attributes;
    }

    /**
     * Standard relation definitions for correction type. Strips _id from
     * the defined attributes
     *
     * @return array
     */
    protected function getCorrectionTypeRelations(): array
    {
        return array_map(function ($attribute) {
            return substr($attribute, 0, -3);
        }, $this->getCorrectionTypeAttributes());
    }

    /**
     * Common rules for the correction type attribute(s)
     * @return array
     */
    protected function rulesForCorrectionType()
    {
        $attr_str = implode(', ', $this->getCorrectionTypeAttributes());
        return [
            [
                $attr_str, 'exist', 'allowEmpty' => true, 'attributeName' => 'id',
                'className' => CorrectionType::class,
                'message' => '{attribute} is invalid'
            ],
            [
                $attr_str, 'safe'
            ],
        ];
    }

    /**
     * Relations definition for the correction type attribute(s)
     *
     * @return array
     */
    protected function relationsForCorrectionType()
    {
        $relation_names = $this->getCorrectionTypeRelations();
        $definitions = [];

        foreach ($this->getCorrectionTypeAttributes() as $i => $attr) {
            $definitions[$relation_names[$i]] = [self::BELONGS_TO, CorrectionType::class, $attr];
        }

        return $definitions;
    }
}
