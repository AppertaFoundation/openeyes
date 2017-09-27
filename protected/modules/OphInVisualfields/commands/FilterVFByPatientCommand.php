<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class FilterVFByPatientCommand extends CConsoleCommand
{
    public function actionIndex($path)
    {
        Yii::app()->event->dispatch('start_batch_mode');
        $count = 0;
        if (!file_exists("$path/unknown")) {
            mkdir("$path/unknown", 0777, true);
        }
        if (!file_exists("$path/error")) {
            mkdir("$path/error", 0777, true);
        }
        if (!file_exists("$path/found")) {
            mkdir("$path/found", 0777, true);
        }
        foreach (glob("$path/*.fmes") as $file) {
            $basename = basename($file);
            $field = file_get_contents($file);
            if ($count % 100 == 0) {
                echo "\n$count:";
            }
            ++$count;

            // Extract the patient number
            $matches = array();
            preg_match('/__OE_PATIENT_ID_([0-9]*)__/', $field, $matches);
            if (count($matches) < 2) {
                rename($file, "$path/error/$basename");
                echo 'e';
                continue;
            }

            // Fetch the patient
            $hos_num = str_pad($matches[1], 7, '0', STR_PAD_LEFT);
            $patient_id = Yii::app()->db->createCommand('select id from patient where hos_num = :hos_num')->queryColumn(array(':hos_num' => $hos_num));
            if ($patient_id) {
                rename($file, "$path/found/$basename");
                echo '!';
            } else {
                rename($file, "$path/unknown/$basename");
                echo '.';
            }
        }
    }
}
