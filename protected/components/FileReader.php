<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2023
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

class FileReader
{
    private static int $SLEEP_SECONDS = 1;

    public static function readFileFromPath($file_path, $rotate = null, $maximum_try_count = 3): bool|string|null
    {
        $try_count = 0;

        $file = null;

        while ($try_count <= $maximum_try_count && is_null($file)) {

            if ($try_count > 0) {
                sleep(self::$SLEEP_SECONDS);
            }

            $try_count++;
            $file_handle = false;
            try {
                $file_handle = self::openFile($file_path);

                if (!$file_handle) {
                    continue;
                } else {
                    $image_size = self::getImageSize($file_path);

                    if (is_null($image_size)) {
                        continue;
                    }

                    $mime = $image_size['mime'] ?? null;
                    if (($mime === 'image/jpeg' || $mime === 'image/png') && $rotate) {
                        $file = self::getRotatedImageFile($file_path, $mime, $rotate);
                    } else {
                        $file = self::getFile($file_handle);
                    }

                    self::closeFile($file_handle);
                }

            } catch (Throwable $throwable) {
                OELog::log($throwable->__toString());
            } finally {
                if (is_resource($file_handle)) {
                    self::closeFile($file_handle);
                }
            }
        }

        return $file;
    }

    private static function getImageFrom($filepath, $mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filepath);
            case 'image/png':
                return imagecreatefrompng($filepath);
            default:
                throw new Exception('Invalid mime type of ' . $mime . ' for ' . $filepath);
        }
    }

    private static function dumpImageAs($image, $mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($image);
                break;
            case 'image/png':
                imagepng($image);
                break;
            default:
                throw new Exception('Invalid mime type of ' . $mime . ' for creating an image');
        }
    }

    /**
     * @param $file_path
     * @return false|resource
     */
    private static function openFile($file_path)
    {
        return fopen($file_path, 'rb');
    }

    /**
     * @param $file_path
     * @param string $mime
     * @param mixed $rotate
     * @return false|string
     * @throws Exception
     */
    private static function getRotatedImageFile($file_path, string $mime, mixed $rotate): string|false
    {
        ob_start();
        $original = self::getImageFrom($file_path, $mime);
        $rotated = imagerotate($original, $rotate, imageColorAllocateAlpha($original, 255, 255, 255, 127));
        self::dumpImageAs($rotated, $mime);
        return ob_get_clean();
    }

    /**
     * @param $handle
     */
    private static function getFile($handle)
    {
        ob_start();
        $buffer_closed = false;
        while (!feof($handle)) {
            $buffer = fread($handle, 4096);
            if (!$buffer) {
                $buffer_closed = true;
                break;
            }
            echo $buffer;
        }

        if ($buffer_closed) {
            ob_end_clean();
            return null;
        }

        return ob_get_clean();
    }

    /**
     * @param $file_path
     * @return bool|array
     */
    private static function getImageSize($file_path): null|bool|array
    {
        $onError = function ($level, $message, $file, $line) {
            throw new ErrorException($message, 0, $level, $file, $line);
        };

        try {
            set_error_handler($onError);
            $image_size = getimagesize($file_path);
        } catch (Throwable $throwable) {
            OELog::logException($throwable);
            $image_size = null;
        } finally {
            restore_error_handler();
        }

        return $image_size;
    }

    /**
     * @param $file_handle
     */
    private static function closeFile($file_handle): void
    {
        fclose($file_handle);
    }
}