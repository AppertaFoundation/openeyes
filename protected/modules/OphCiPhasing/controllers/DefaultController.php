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

namespace OEModule\OphCiPhasing\controllers;

use BaseEventTypeController;

use Eye;

use OEModule\OphCiPhasing\models\OphCiPhasing_Reading;

class DefaultController extends BaseEventTypeController
{
    /**
     * Parse the data for the phasing readings.
     *
     * @param int   $eye_id
     * @param array $data
     *
     * @return array
     */
    private function _parseReadingData($eye_id, array $data)
    {
        $readings_by_side = array(OphCiPhasing_Reading::RIGHT => array(), OphCiPhasing_Reading::LEFT => array());

        $sides = array();
        if ($eye_id == Eye::LEFT || $eye_id == Eye::BOTH) {
            $sides[] = OphCiPhasing_Reading::LEFT;
        }
        if ($eye_id == Eye::RIGHT || $eye_id == Eye::BOTH) {
            $sides[] = OphCiPhasing_Reading::RIGHT;
        }

        foreach ($data['intraocularpressure_reading'] as $item) {
            if (in_array($item['side'], $sides)) {
                $readings_by_side[$item['side']][] = $item;
            }
        }

        return $readings_by_side;
    }

    /**
     * Set the reading elements for validation.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphCiPhasing_IntraocularPressure($element, $data, $index)
    {
        $data = $this->_parseReadingData($element->eye_id, $data);

        foreach ($data as $side => $items) {
            $readings = array();
            foreach ($items as $item) {
                $item_model = new OphCiPhasing_Reading();
                $item_model->measurement_timestamp = $item['measurement_timestamp'];
                $item_model->side = $item['side'];
                $item_model->value = $item['value'];
                $readings[] = $item_model;
            }
            if ($side == OphCiPhasing_Reading::RIGHT) {
                $element->right_readings = $readings;
            } else {
                $element->left_readings = $readings;
            }
        }
    }

    /**
     * Save the readings to the element.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphCiPhasing_IntraocularPressure($element, $data, $index)
    {
        $data = $this->_parseReadingData($element->eye_id, $data);
        $element->updateReadings(OphCiPhasing_Reading::RIGHT, $data[OphCiPhasing_Reading::RIGHT]);
        $element->updateReadings(OphCiPhasing_Reading::LEFT, $data[OphCiPhasing_Reading::LEFT]);
    }
}
