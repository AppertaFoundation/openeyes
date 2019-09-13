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
class FixFMESCommand extends CConsoleCommand
{
    public function actionIndex($path, $subfolder)
    {
        $count = 0;
        mkdir("$path/archive/$subfolder", 0777, true);
        mkdir("$path/finished/$subfolder", 0777, true);
        mkdir("$path/xml-missing/$subfolder", 0777, true);
        mkdir("$path/error/$subfolder", 0777, true);
        foreach (glob($path.'/incoming/'.$subfolder.'/*.fmes') as $file) {
            $basename = basename($file);
            $xml = simplexml_load_file($file);
            $image_data = base64_decode($xml->image_scan_data['value']);
            if (!$image_data) {
                echo "Bad image data in $basename, moving to error\n";
                rename($file, "$path/error/$subfolder/$basename");
                continue;
            }
            $image = imagecreatefromstring($image_data);
            $thumb = imagecreatetruecolor(776, 864);
            imagecopy($thumb, $image, 0, 0, 1328, 560, 776, 864);
            ob_start();
            imagegif ($thumb);
            $thumb_data = base64_encode(ob_get_contents());
            ob_end_clean();
            imagedestroy($thumb);
            imagedestroy($image);

            // Check XML
            $target = 'finished';
            if (!$xml->xml_file_data || !$xml->xml_file_data['value']) {
                echo "$basename is missing XML\n";
                $target = 'xml-missing';
            }

            // Write updated file
            $xml->image_scan_crop_data['value'] = $thumb_data;
            $xml->asXml("$path/$target/$subfolder/$basename");
            rename($file, "$path/archive/$subfolder/$basename");
            if ($count % 20 == 0) {
                echo $count."\n";
            }
            ++$count;
        }
    }
}
