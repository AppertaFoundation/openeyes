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
 * @property string $file_content
 *
 * @property ProtectedFileThumbnail $thumbnails
 */
class ProtectedFile extends BaseActiveRecordVersioned
{
    const THUMBNAIL_QUALITY = 85;

    /**
     * Create a new protected file from an existing file.
     *
     * @param string $path Path to file
     *
     * @return ProtectedFile
     * @throws CException
     */
    public static function createFromFile($path)
    {
        $file = new self();
        $file->file_content = file_get_contents($path);
        $file->setSource($path);

        return $file;
    }

    /**
     * create a new protected file object which has properties that can be used for writing an actual file to.
     *
     * @param string $name
     *
     * @return ProtectedFile
     * @throws Exception
     */
    public static function createForWriting($name)
    {
        $file = new self();
        $file->name = $name;

        $file->generateUID();

        return $file;
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
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
                array('uid, name, title, mimetype, size', 'required'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'thumbnails' => array(self::HAS_MANY, 'ProtectedFileThumbnail', 'file_id')
        );
    }

    /**
     * Path to protected file storage.
     *
     * @return string
     */
    public static function getBasePath()
    {
        return Yii::app()->basePath.'/files';
    }

    /**
     * Path to file without filename.
     *
     * @return string
     */
    public function getFilePath()
    {
        return self::getBasePath().'/'. $this->uid[0]
        .'/'. $this->uid[1] .'/'. $this->uid[2];
    }

    /**
     * Path to file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getFilePath()
        .'/'.$this->uid;
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
        $this->uid = sha1(microtime().$this->name);
    }

    /**
     * Initialise protected file from a source file.
     *
     * @param string $path
     *
     * @throws CException
     * @throws Exception
     */
    public function setSource($path)
    {
        if (!file_exists($path) || is_dir($path)) {
            throw new CException("File doesn't exist: ".$path);
        }

        $this->name = basename($path);

        // Set MIME type
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
     * @param bool $regenerate
     * @return bool|ProtectedFile|null
     * @throws CException
     */
    public function getThumbnail($dimensions, $regenerate = false)
    {
        preg_match('/\d+(x\d+)?/', $dimensions, $matches);
        if (!$matches) {
            throw new CException('Invalid thumbnail dimensions');
        }
        $dimensions_parts = explode('x', $dimensions);
        $thumbnail = ProtectedFileThumbnail::model()->findByAttributes(
            array(
                'file_id' => $this->id,
                'width' => $dimensions_parts[0],
                'height' => $dimensions_parts[1],
            )
        );
        if ($regenerate || !$thumbnail) {
            if (!$this->generateThumbnail($dimensions)) {
                return false;
            }
            $this->refresh();
            $thumbnail = ProtectedFileThumbnail::model()->findByAttributes(
                array(
                    'file_id' => $this->id,
                    'width' => $dimensions_parts[0],
                    'height' => $dimensions_parts[1],
                )
            );
        }

        return $thumbnail;
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
        return self::getBasePath().'/'. $this->uid[0]
        .'/'. $this->uid[1] .'/'. $this->uid[2]
        .'/'.$dimensions.'/'.$this->uid;
    }

    /**
     * Generate a thumbnail.
     *
     * @param string $dimensions
     *
     * @return bool
     * @throws Exception
     *
     * @throws CException
     */
    protected function generateThumbnail($dimensions)
    {
        // Setup source image
        $image_info = getimagesize($this->getPath());
        $src_width = $image_info[0];
        $src_height = $image_info[1];
        $ratio = $src_width / $src_height;
        $image_type = $image_info[2];

        file_put_contents($this->getPath(), $this->file_content);

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
            $concurrent_directory = dirname($thumbnail_path);
            if (!mkdir($concurrent_directory, 0777, true) && !is_dir($concurrent_directory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrent_directory));
            }
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

        $thumbnail_obj = new ProtectedFileThumbnail();
        $thumbnail_obj->file_id = $this->id;
        $thumbnail_obj->width = $dimensions_parts[0];
        $thumbnail_obj->height = $dimensions_parts[1];
        $thumbnail_obj->size = filesize($thumbnail_path);
        $thumbnail_obj->thumbnail_content = file_get_contents($thumbnail_path);

        if (!$thumbnail_obj->save()) {
            throw new CException('Unable to save thumbnail.');
        }

        $this->refresh();

        unlink($thumbnail_path);
        unlink($this->getPath());

        return true;
    }
}
