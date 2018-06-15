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
class WKHtmlToX
{
    protected $param_key;

    protected $application_name;
    protected $application_path;


    protected $documents = 1;
    protected $docrefs = array();
    protected $barcodes = array();
    protected $patients = array();
    protected $canvas_image_path;
    public $custom_tags = array();

    public function __construct()
    {
        if (Yii::app()->params['wkhtmltox'][$this->param_key]['path']) {
            if (!file_exists(Yii::app()->params['wkhtmltox'][$this->param_key]['path'])) {
                if (!$this->application_path = trim(`which $this->application_name`)) {
                    throw new Exception($this->application_name . ' not found in the current path.');
                }
            } else {
                $this->application_path = Yii::app()->params['wkhtmltox'][$this->param_key]['path'];
            }
        }

        $banner = $this->execute($this->application_path . ' 2>&1');

        if (preg_match('/reduced functionality/i', $banner)) {
            throw new Exception($this->application_name . ' has not been compiled with patched QT and so cannot be used.');
        }
    }

    protected function execute($command)
    {
        return shell_exec($command);
    }

    public function getAssetManager()
    {
        return Yii::app()->assetManager;
    }

    public function remapAssetPaths($html)
    {
        $html = str_replace('href="/assets/', 'href="' . $this->getAssetManager()->basePath . '/', $html);
        $html = str_replace('src="/assets/', 'src="' . $this->getAssetManager()->basePath . '/', $html);

        return $html;
    }

    public function remapCanvasImagePaths($html)
    {
        preg_match_all('/<img src="\/.*?\/default\/eventImage\?event_id=[0-9]+&image_name=(.*?)"/', $html, $m);

        foreach ($m[0] as $i => $img) {
            $html = str_replace($img, "<img src=\"$this->canvas_image_path/{$m[1][$i]}.png\"", $html);
        }

        return $html;
    }

    public function findOrCreateDirectory($path)
    {
        if (!file_exists($path)) {
            if (!@mkdir($path, 0755, true)) {
                throw new Exception("Unable to create directory: $path: check permissions.");
            }
        }
    }

    public function readFile($path)
    {
        if (!$data = @file_get_contents($path)) {
            throw new Exception("File not found: $path");
        }

        return $data;
    }

    public function writeFile($path, $data)
    {
        if (!@file_put_contents($path, $data)) {
            throw new Exception("Unable to write to $path: check permissions.");
        }
    }

    public function deleteFile($path)
    {
        if (@file_exists($path)) {
            if (!@unlink($path)) {
                throw new Exception("Unable to delete $path: check permissions.");
            }
        }
    }

    public function fileExists($path)
    {
        return @file_exists($path);
    }

    public function fileSize($path)
    {
        return @filesize($path);
    }

    public function setCanvasImagePath($image_path)
    {
        $this->canvas_image_path = $image_path;
    }
}
