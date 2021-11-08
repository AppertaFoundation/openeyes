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

namespace OEModule\OphCiExamination\tests\traits;


use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\RedReflex;

trait InteractsWithRedReflex
{
    use \WithFaker;
    use \InteractsWithEventTypeElements;

    public function generateSavedRedReflex($attrs = []): RedReflex
    {
        $element = new RedReflex();

        if (!isset($attrs['eye_id'])) {
            $element->setHasLeft();
            $element->setHasRight();
        }
        $element->setAttributes($this->generateRedReflexData($attrs));

        return $this->saveElement($element);
    }

    public function generateRedReflexData($attrs = [])
    {
        if (!isset($attrs['eye_id'])) {
            $attrs['eye_id'] = $this->faker->randomElement([SidedData::RIGHT, SidedData::LEFT, SidedData::RIGHT | SidedData::LEFT]);
        }
        if (RedReflex::isValueForRight($attrs['eye_id'])) {
            $attrs['right_has_red_reflex'] ??= $this->faker->randomElement([RedReflex::HAS_RED_REFLEX, RedReflex::NO_RED_REFLEX]);
        } else {
            unset($attrs['right_has_red_reflex']);
        }

        if (RedReflex::isValueForLeft($attrs['eye_id'])) {
            $attrs['left_has_red_reflex'] ??= $this->faker->randomElement([RedReflex::HAS_RED_REFLEX, RedReflex::NO_RED_REFLEX]);
        } else {
            unset($attrs['left_has_red_reflex']);
        }

        return $attrs;
    }
}
