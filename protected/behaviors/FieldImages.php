<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class FieldImages extends CActiveRecordBehavior
{
	private $imgTypes = array('jpg');
	private $cFile;
	const FIELDS_IMAGES_ALIAS ='application.assets.img.fieldImages';


	/*
	 * @param string path - optional parameter for alias injection while testing
	 * @return array - list of field images for class
	 */
	public function getFieldImages(  $cFile = null, $assetManager = null)
	{
		if(!method_exists($this->owner, 'fieldImages')){
			throw new FieldImagesException('fieldImages method not implemented in : ' . get_class($this->owner));
		}

		if(!$cFile){// injection to allow function mocking
			$cFile = 'CFileHelper';
		}

		if(!$assetManager){// injection to allow function mocking
			$assetManager = Yii::app()->assetManager;
		}

		$imgsPath = Yii::getPathOfAlias(self::FIELDS_IMAGES_ALIAS);

		$imgs = $cFile::findFiles($imgsPath, array('fileTypes' => $this->imgTypes));

		return $this->getMatchingImgs($imgs, $assetManager);
	}

	private function getMatchingImgs($imgs, $assetManager){
		$matchImgs = array();
		$className = CHtml::modelName($this->owner);
		$fields = implode( '|',$this->owner->fieldImages() );

		//\bname_name-(filippo|pino)-(6|5|9).jpg+\b

		$pattern = "/" . $className . "-" . "(" . $fields . ")-(.*).jpg/i";

		foreach($imgs as $img){
			if(preg_match($pattern, $img, $matches)){
				$matchImgs[$matches[2]]= $assetManager->getPublishedPathOfAlias(self::FIELDS_IMAGES_ALIAS) .
					DIRECTORY_SEPARATOR . basename($matches[0]);
			}
		}

		if(empty($matchImgs)){
			return array();
		}
		return $matchImgs;
	}
}