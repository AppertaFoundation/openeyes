<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "asset".
 *
 * The followings are the available columns in table 'asset':
 * @property string $id
 * @property string $uid
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $mimetype
 * @property integer $size
 */
class Asset extends BaseActiveRecord {

	protected $_source_path;

	/**
	 * Create a new asset from an existing file
	 * @param string $path Path to file
	 * @return Asset
	 */
	public static function createFromFile($path) {
		$asset = new Asset();
		$asset->setSource($path);
		return $asset;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return Asset the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'asset';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
				array('uid, name, mimetype, size', 'required'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
		);
	}

	public static function getBasePath() {
		return Yii::app()->basePath . '/assets';
	}

	public function getPath() {
		return self::getBasePath() . '/' . substr($this->uid, 0, 1)
		. '/' . substr($this->uid, 1, 1) . '/' . substr($this->uid, 2, 1)
		. '/' . $this->uid;
	}

	protected function beforeSave() {
		if($this->_source_path) {
			mkdir(dirname($this->getPath()), 777, true);
			copy($this->_source_path, $this->getPath());
			$this->_source_path = null;
		}
		return true;
	}

	public function setSource($path) {

		if(!file_exists($path) || is_dir($path)) {
			throw new CException("File doesn't exist: ".$path);
		}
		$this->_source_path = $path;

		$this->name = basename($path);

		// Set MIME type
		$path_parts = pathinfo($this->name);
		$this->mimetype = $this->lookupMimetype($path);
			
		// Set size
		$this->size = filesize($path);

		// Set UID
		$this->uid = sha1(microtime().$this->name);
		while(file_exists($this->getPath())) {
			$this->uid = sha1(microtime().$this->name);
		}

	}

	protected function lookupMimetype($path) {
		$finfo = new finfo(FILEINFO_MIME);
		return $finfo->file($path);
	}

}