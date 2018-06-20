<?php

/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class WKHtmlToImage extends WKHtmlToX
{
    public function __construct()
    {
        $this->param_key = 'image';
        $this->application_name = 'wkhtmltoimage';

        parent::__construct();
    }


    public function generateImage(
        $imageDirectory,
        $prefix,
        $suffix,
        $html,
        $options = array(),
        $output_html = false
    ) {
        if(!$output_html) {
            $html = $this->remapAssetPaths($html);
            $html = $this->remapCanvasImagePaths($html);
        }

        $this->findOrCreateDirectory($imageDirectory);

        $html_file = $suffix ? "$imageDirectory" . DIRECTORY_SEPARATOR . "{$prefix}_$suffix.html" : "$imageDirectory" . DIRECTORY_SEPARATOR . "$prefix.html";
        $image_file = $suffix ? "$imageDirectory" . DIRECTORY_SEPARATOR . "{$prefix}_$suffix.png" : "$imageDirectory" . DIRECTORY_SEPARATOR . "$prefix.png";

        $this->writeFile($html_file, $html);

        $cmd_str = escapeshellarg($this->application_path);
        if (array_key_exists('width', $options)) {
            $cmd_str .= ' --width ' . $options['width'] . ' --disable-smart-width ';
        }

        if(array_key_exists('quality', $options)) {
            $cmd_str .= ' --quality ' . $options['quality'];
        }

        $cmd_str .= ' ' . escapeshellarg($html_file) . ' ' . escapeshellarg($image_file) . ' 2>&1';

        $res = $this->execute($cmd_str);

        if (!$this->fileExists($image_file) || $this->fileSize($image_file) == 0) {
            if ($this->fileSize($image_file) == 0) {
                $this->deleteFile($image_file);
            }

            throw new Exception("Unable to generate $image_file: $res");
        }

        $this->deleteFile($html_file);

        return true;
    }

    public function setCustomTag($tag_name, $value)
    {
        $this->custom_tags[$tag_name] = $value;
    }
}
