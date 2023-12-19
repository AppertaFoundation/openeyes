<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2023
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class PrescriptionPrintPageCountCalculator
{
    private const DISABLE_PRINT_NOTES_COPY_SETTING = 'disable_print_notes_copy';
    private const DISABLE_PRESCRIPTION_PATIENT_COPY = 'disable_prescription_patient_copy';

    public static function calculatePageCountWithSettings(): int
    {
        $document_count = 1;
        if (SettingMetadata::model()->getSetting(self::DISABLE_PRINT_NOTES_COPY_SETTING) === SettingMetadata::$OFF_SETTING_VALUE) {
            $document_count++;
        }

        if (\SettingMetadata::model()->getSetting(self::DISABLE_PRESCRIPTION_PATIENT_COPY) === SettingMetadata::$OFF_SETTING_VALUE) {
            $document_count++;
        }

        return $document_count;
    }
}
