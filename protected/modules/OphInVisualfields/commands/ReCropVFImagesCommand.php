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
class ReCropVFImagesCommand extends CConsoleCommand
{
    public function getHelp()
    {
        return 'Usage: recropvfimages crop --dims=[x,y,w,h]'
        ."\n\nRecrops all VF images to required dimensions, and also ensures images sizes in db are correct\n";
    }

    public function actionCrop($dims = '1328x560,776x864')
    {
        $dimensions = explode(',', $dims);
        $xy = explode('x', $dimensions[0]);
        $wh = explode('x', $dimensions[1]);
        $src_x = $xy[0];
        $src_y = $xy[1];
        $dest_w = $wh[0];
        $dest_h = $wh[1];
        // find all cropped images in ophinvisualfields_field_measurement->cropped_image_id:
        $fields = OphInVisualfields_Field_Measurement::model()->findAll();
        foreach ($fields as $field) {
            $full = ProtectedFile::model()->findByPk($field->image_id);
            $cropped = ProtectedFile::model()->findByPk($field->cropped_image_id);
            // if the value isnt set, move on
            if (!$full || !$cropped) {
                continue;
            }

            // next step, take image_id and open image:
            if (file_exists($full->getPath())) {
                $src = imagecreatefromgif ($full->getPath());
                $dest = imagecreatetruecolor($dest_w, $dest_h);
                imagecopy($dest, $src, 0, 0, $src_x, $src_y, $dest_w, $dest_h);
                imagegif ($dest, $cropped->getPath());
                echo 'patient: '.$field->getPatientMeasurement()->patient->hos_num.', path: '.$cropped->getPath().PHP_EOL;

                // Reset sizes
                $full->size = filesize($full->getPath());
                $full->save();
                $cropped->size = filesize($cropped->getPath());
                $cropped->save();
            }
        }
    }
}
