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

/**
 * This is the model class for table "protected_file".
 *
 * The followings are the available columns in table 'protected_file':
 *
 * @property int $id
 * @property string $uid
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $mimetype
 * @property int $size
 */
class ProtectedFile extends BaseActiveRecordVersioned
{
    protected const THUMBNAIL_QUALITY = 85;
    // used in model creation
    protected $source_path;
    // used in model delete
    protected $stored_path;
    protected $protected_thumbnail = array();

    /**
     * Create a new protected file from an existing file.
     *
     * @param string $path Path to file
     *
     * @return ProtectedFile
     */
    public static function createFromFile($path)
    {
        $file = new self();
        $file->setSource($path);

        return $file;
    }

    /**
     * create a new protected file object which has properties that can be used for writing an actual file to.
     *
     * @param string $name
     *
     * @return ProtectedFile
     */
    public static function createForWriting($name)
    {
        $file = new self();
        $file->name = $name;

        $file->generateUID();

        $path = $file->getFilePath();
        if (!file_exists($path)) {
            if (!@mkdir($path, 0775, true)) {
                throw new Exception("$path could not be created: permission denied");
            }
        }

        return $file;
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @return ProtectedFile the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'protected_file';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('uid, name, mimetype, size', 'required'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
        );
    }

    /**
     * Path to protected file storage.
     *
     * @return string
     */
    public static function getBasePath()
    {
        return Yii::app()->basePath . '/files';
    }

    /**
     * Path to file without filename.
     *
     * @return string
     */
    public function getFilePath()
    {
        return self::getBasePath() . '/' . substr($this->uid, 0, 1)
        . '/' . substr($this->uid, 1, 1) . '/' . substr($this->uid, 2, 1);
    }

    /**
     * Path to file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getFilePath()
        . '/' . $this->uid;
    }

    /**
     * get URL for downloading this file.
     *
     * @see ProtectedFileController::actionDownload
     *
     * @return string
     */
    public function getDownloadURL()
    {
        return Yii::app()->createURL('ProtectedFile/Download', array('id' => $this->id));
    }

    /**
     * ensures that mimetype and size are set based on the file that's been stored (unless the file
     * is being copied from elsewhere, in which case this should have been taken care of).
     *
     * (non-PHPdoc)
     *
     * @see BaseActiveRecord::beforeSave()
     */
    public function beforeValidate()
    {
        if (!$this->source_path) {
            // the file should be in place
            $path = $this->getPath();

            if (!$this->mimetype) {
                $this->mimetype = $this->lookupMimetype($path);
            }
            if (!$this->size) {
                $this->size = filesize($path);
            }
        }

        return parent::beforeValidate();
    }

    /**
     * (non-PHPdoc).
     *
     * @see BaseActiveRecord::beforeSave()
     */
    public function beforeSave()
    {
        if ($this->source_path) {
            if (!file_exists(dirname($this->getPath()))) {
                mkdir(dirname($this->getPath()), 0777, true);
            }
            copy($this->source_path, $this->getPath());
            $this->source_path = null;
        } elseif (!file_exists($this->getPath())) {
            throw new Exception('There has been an error with file storage');
        }

        return true;
    }

    /**
     * ensure we keep track of path to file before deleting from database.
     *
     * @return bool
     *              (non-PHPdoc)
     *
     * @see BaseActiveRecord::beforeDelete()
     */
    public function beforeDelete()
    {
        $this->stored_path = $this->getPath();

        return parent::beforeDelete();
    }

    /**
     * ensures file is removed from filesystem when deleting.
     */
    public function afterDelete()
    {
        unlink($this->stored_path);

        return parent::afterDelete();
    }

    /**
     * generate the UID for the file from the file name.
     *
     * @throws Exception
     */
    public function generateUID()
    {
        if (!$this->name) {
            throw new Exception('ProtectedFile requires name attribute to generate storage parameters');
        }

        // Set UID
        $this->uid = sha1(microtime() . $this->name);
        while (file_exists($this->getPath())) {
            $this->uid = sha1(microtime() . $this->name);
        }
    }

    /**
     * Initialise protected file from a source file.
     *
     * @param string $path
     *
     * @throws CException
     */
    public function setSource($path)
    {
        if (!file_exists($path) || is_dir($path)) {
            throw new CException("File doesn't exist: " . $path);
        }
        $this->source_path = $path;

        $this->name = basename($path);

        // Set MIME type
        $path_parts = pathinfo($this->name);
        $this->mimetype = $this->lookupMimetype($path);

        // Set size
        $this->size = filesize($path);

        // UID
        $this->generateUID();
    }

    /**
     * Get the mime type of the file.
     *
     * @param string $path
     *
     * @return string
     */
    protected function lookupMimetype($path)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        return $finfo->file($path);
    }

    /**
     * Has the file got a thumbnail.
     *
     * @return bool
     */
    public function hasThumbnail()
    {
        return in_array($this->mimetype, array(
                'image/jpeg',
                'image/gif',
                'image/png',
        ));
    }

    /**
     * Get thumbnail of image (generated automatically if not already created).
     *
     * @param string $dimensions
     *
     * @throws CException
     *
     * @return bool|array:
     */
    public function getThumbnail($dimensions, $regenerate = false)
    {
        preg_match('/\d+(x\d+)?/', $dimensions, $matches);
        if (!$matches) {
            throw new CException('Invalid thumbnail dimensions');
        }
        if ($regenerate || !isset($this->protected_thumbnail[$dimensions])) {
            $path = $this->getThumbnailPath($dimensions);
            if ($regenerate || !file_exists($path)) {
                if (!$this->generateThumbnail($dimensions)) {
                    return false;
                }
            }
            $this->protected_thumbnail[$dimensions] = array(
                    'path' => $path,
                    'size' => filesize($path),
            );
        }

        return $this->protected_thumbnail[$dimensions];
    }

    /**
     * Get the path for a thumbnail.
     *
     * @param string $dimensions
     *
     * @return string
     */
    protected function getThumbnailPath($dimensions)
    {
        return self::getBasePath() . '/' . substr($this->uid, 0, 1)
        . '/' . substr($this->uid, 1, 1) . '/' . substr($this->uid, 2, 1)
        . '/' . $dimensions . '/' . $this->uid;
    }

    /**
     * Generate a thumbnail.
     *
     * @param string $dimensions
     *
     * @throws CException
     *
     * @return bool
     */
    protected function generateThumbnail($dimensions)
    {
        // Setup source image
        $image_info = getimagesize($this->getPath());
        $src_width = $image_info[0];
        $src_height = $image_info[1];
        $ratio = $src_width / $src_height;
        $image_type = $image_info[2];

        // Work out thumbnail width/height
        $dimensions_parts = explode('x', $dimensions);
        if (!$width = (int) $dimensions_parts[0]) {
            throw new CException('Bad width');
        }
        if (isset($dimensions_parts[1])) {
            if (!$height = (int) $dimensions_parts[1]) {
                throw new CException('Bad height');
            }
        } else {
            $height = floor($width / $ratio);
        }

        $thumbnail = imagecreatetruecolor($width, $height);
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $src_image = imagecreatefromjpeg($this->getPath());
                break;
            case IMAGETYPE_PNG:
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $src_image = imagecreatefrompng($this->getPath());
                break;
            case IMAGETYPE_GIF:
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $src_image = imagecreatefromgif($this->getPath());
                break;
            default:
                return false;
        }

        // Generate thumbnail
        imagecopyresampled($thumbnail, $src_image, 0, 0, 0, 0, $width, $height, $src_width, $src_height);
        $thumbnail_path = $this->getThumbnailPath($dimensions);
        if (!file_exists(dirname($thumbnail_path))) {
            mkdir(dirname($thumbnail_path), 0777, true);
        }
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnail, $thumbnail_path, self::THUMBNAIL_QUALITY);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnail, $thumbnail_path, self::THUMBNAIL_QUALITY * 9 / 100);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumbnail, $thumbnail_path);
                break;
        }

        return true;
    }
}
