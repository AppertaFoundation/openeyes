<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories\models;

use CDbCriteria;
use OE\factories\ModelFactory;

/**
 * The definition for this has not been expanded, as it's assumed it will always be used with the "forExisting"
 * behaviour to retrieve an ElementType from the database.
 */
class ElementTypeFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }

    protected function getExisting($attributes = [])
    {
        $modelName = $this->modelName();
        $criteria = new CDbCriteria();
        $criteria->order = 'RAND()';
        $criteria->limit = $this->count ?? 1;

        $this->constrainCriteriaToAvailableEvents($criteria, $attributes);

        return $modelName::model()
            ->with('eventType')
            ->findAll($criteria);
    }

    private function constrainCriteriaToAvailableEvents(CDbCriteria $criteria, array $attributes): void
    {
        $qualified_attributes = [];
        foreach ($attributes as $key => $value) {
            $qualified_attributes["t.$key"] = $value;
        }
        $criteria->addColumnCondition($qualified_attributes);

        // use app module config to constrain the event type class
        $criteria->addInCondition(
            'eventType.class_name',
            array_keys($this->app->getModules())
        );
    }
}
