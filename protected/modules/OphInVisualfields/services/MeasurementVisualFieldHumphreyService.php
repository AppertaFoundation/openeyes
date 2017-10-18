<?php

/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphInVisualfields\services;

class MeasurementVisualFieldHumphreyService extends \services\ModelService
{
    protected static $operations = array(self::OP_CREATE);

    protected static $primary_model = 'OphInVisualfields_Field_Measurement';

    public function resourceToModel($res, $measurement)
    {
        $measurement->eye_id = $res->eye_id;

        $pattern = \OphInVisualfields_Pattern::model()->find('name=:name', array(':name' => $res->pattern));
        if (!$pattern) {
            throw new \Exception("Unrecognised test pattern: '{$res->pattern}'");
        }
        $measurement->pattern_id = $pattern->id;

        $strategy = \OphInVisualfields_Strategy::model()->find('name=:name', array(':name' => $res->strategy));
        if (!$strategy) {
            throw new \Exception("Unrecognised test strategy: '{$res->strategy}'");
        }
        $measurement->strategy_id = $strategy->id;

        $measurement->study_datetime = $res->study_datetime;
        $measurement->cropped_image_id = $res->scanned_field_crop_id;
        $measurement->image_id = $res->scanned_field_id;
        if (isset($res->xml_file_data)) {
            $measurement->source = base64_decode($res->xml_file_data);
        }
        $measurement->patient_id = $res->patient_id;
        $this->saveModel($measurement);
    }
}
