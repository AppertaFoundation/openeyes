<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

trait FileHelperTrait
{
    /**
     * Creates and saves Protected file from HTMLCanvasElement.toDataURL()
     *
     * @param $file_content
     * @param $name
     * @return ProtectedFile
     * @throws Exception
     */
    public function createProtectedFileFromDataURL($file_content, $name): ProtectedFile
    {
        $return = explode(';', $file_content);
        if (sizeof($return) > 1) {
            $type = $return[0];

            list(, $file_content) = explode(',', $file_content);
            $file_content = base64_decode($file_content);

            $file_extension = explode("/", $type);
            $tmp_name = '/tmp/' . $name . '.' . $file_extension[1];

            file_put_contents($tmp_name, $file_content);

            $p_file = ProtectedFile::createFromFile($tmp_name);
            $p_file->name = $name;
            $p_file->title = $name;

            if ($p_file->save()) {
                unlink($tmp_name);
                return $p_file;
            } else {
                throw new Exception('Unable to create ProtectedFile: ' . print_r($p_file->getErrors(), true));
            }
        } else {
            throw new Exception('Unable to create ProtectedFile: no image data');
        }
    }
}
