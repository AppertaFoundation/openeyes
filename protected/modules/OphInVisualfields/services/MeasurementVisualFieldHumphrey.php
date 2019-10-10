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

class MeasurementVisualFieldHumphrey extends \services\Resource
{
    public $id;
    public $study_datetime;
    public $patient_id;
    public $eye_id;
    public $strategy;
    public $pattern;
    public $scanned_field_id;
    public $scanned_field_crop_id;

    public static function fromFhir($fhirObject)
    {
        $report = parent::fromFhir($fhirObject);

        $patient = \Patient::model()->find('id=?', array($report->patient_id));
        $report->patient_id = $patient->id;
        $eye = 'Right';
        if ($report->eye == 'L') {
            $eye = 'Left';
        } elseif ($report->eye == 'B') {
            $eye = 'Both';
        }
        $report->eye_id = \Eye::model()->find('name=:name', array(':name' => $eye))->id;
        if (isset($fhirObject->xml_file_data)) {
            $report->xml_file_data = base64_decode($fhirObject->xml_file_data);
        }

        $title = $report->file_reference;

        if (\ProtectedFile::model()->find('name = ?', array($title))) {
            throw new EverythingsFine("Duplicate filename: {$title} (patient ID {$report->patient_id})");
        }

        $protected_file = \ProtectedFile::createForWriting($title);
        $protected_file->name = $title;
        file_put_contents($protected_file->getPath(), base64_decode($report->image_scan_data));
        $protected_file->mimetype = 'image/gif';
        $protected_file->save();

        $cropped_file = \ProtectedFile::createForWriting($title);
    // all content is base64 encoded, so decode it:
        file_put_contents($cropped_file->getPath(), base64_decode($report->image_scan_crop_data));
        $cropped_file->mimetype = 'image/gif';
        $cropped_file->name = $title;
        $cropped_file->save();

        $report->scanned_field_id = $protected_file->id;
        $report->scanned_field_crop_id = $cropped_file->id;

        return $report;
    }
}
