<?php

/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2018
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
    /**
     * @var string The key used to look up configuration parameters
     */
    protected $param_key;

    /**
     * @var string The name of the application used to generate images
     */
    protected $application_name;
    /**
     * @var string The path of the application used to generate the images (derived from $application_name)
     */
    protected $application_path;

    /**
     * @var string The path in which the canvas images are stored
     */
    protected $canvas_image_path;

    /**
     * WKHtmlToX constructor.
     * @throws Exception
     */
    public function __construct()
    {
        if (Yii::app()->params['wkhtmltox'][$this->param_key]['path']) {
            if (!file_exists(Yii::app()->params['wkhtmltox'][$this->param_key]['path'])) {
                $this->application_path = trim($this->execute("which $this->application_name"));
                if (!$this->application_path) {
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

    /**
     * Executes the given shell command
     *
     * @param string $command The command to execute
     * @return string The result of the command
     */
    protected function execute($command)
    {
        return shell_exec($command);
    }

    /**
     * Gets the asset manager
     *
     * @return CAssetManager The aset manager
     */
    public function getAssetManager()
    {
        return Yii::app()->assetManager;
    }

    /**
     * Replaces asset paths in the given HTML to use absolute paths
     *
     * @param string $html The HTML to transform
     * @return string THe transformed HTML
     */
    public function remapAssetPaths($html)
    {
        $html = str_replace('href="/assets/', 'href="' . $this->getAssetManager()->basePath . '/', $html);
        $html = str_replace('src="/assets/', 'src="' . $this->getAssetManager()->basePath . '/', $html);

        return $html;
    }

    /**
     * Replaces image paths in the given HTML to use $this->>canvas_image_path as the root path
     *
     * @param string $html The HTML to replace
     * @return string The munged HTML
     */
    public function remapCanvasImagePaths($html)
    {
        preg_match_all('/<img src="\/.*?\/default\/eventImage\?event_id=\d+&image_name=(.*?)"/', $html, $m);

        foreach ($m[0] as $i => $img) {
            $html = str_replace($img, "<img src=\"$this->canvas_image_path/{$m[1][$i]}.png\"", $html);
        }

        return $html;
    }

    /**
     * Creates a directory at the given path if it doesn't already exist
     *
     * @param string $path The path of the directory to create
     * @throws Exception Thrown if the directory could not be made
     */
    public function findOrCreateDirectory($path)
    {
        if (!file_exists($path)) {
            if (!@mkdir($path, 0755, true) || !is_dir($path)) {
                throw new Exception("Unable to create directory: $path: check permissions.");
            }
        }
    }

    /**
     * Reads and returns the contents of a file
     *
     * @param string $path The file to read
     * @return string The contents of the file
     * @throws Exception Thrown if the file could not be found or read
     */
    public function readFile($path)
    {
        $data = @file_get_contents($path);
        if (!$data) {
            throw new Exception("File not found: $path");
        }

        return $data;
    }

    /**
     * Writes a data buffer to the given path
     *
     * @param string $path The
     * @param string $data The file contents
     * @throws Exception Thrown if an error occurred when writing the file
     */
    public function writeFile($path, $data)
    {
        if (!@file_put_contents($path, $data)) {
            throw new Exception("Unable to write to $path: check permissions.");
        }
    }

    /**
     * Deletes the path at the given path
     *
     * @param string $path The file to delete
     * @throws Exception Thrown if the file doesn't exist or could not be deleted
     */
    public function deleteFile($path)
    {
        if (@file_exists($path) && !@unlink($path)) {
            throw new Exception("Unable to delete $path: check permissions.");
        }
    }

    /**
     * Gets a value indicating whether a file exists at the given path
     *
     * @param string $path The path to test
     * @return bool Whether teh file exists or not
     */
    public function fileExists($path)
    {
        return @file_exists($path);
    }

    /**
     * Gets the size of the file in the given path in bytes
     *
     * @param string $path The file path
     * @return int The file size in bytes
     */
    public function fileSize($path)
    {
        return @filesize($path);
    }

    /**
     * Sets the path of wher ethe canvas images should be found
     *
     * @param string $image_path
     */
    public function setCanvasImagePath($image_path)
    {
        $this->canvas_image_path = $image_path;
    }
}
