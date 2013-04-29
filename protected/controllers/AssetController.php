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

class AssetController extends BaseController {

	public function accessRules() {
		return array(
				// Level 3 or above can do anything
				array('allow',
						'expression' => 'BaseController::checkUserLevel(3)',
				),
				array('deny'),
		);
	}

	public function actionDownload($id) {
		if(!$asset = Asset::model()->findByPk($id)) {
			throw new CHttpException(404, "Asset not found");
		}
		if(!file_exists($asset->getPath())) {
			throw new CException("Asset not found on filesystem: ".$asset->getPath());
		}
		header("Content-Type: " . $asset->mimetype);
		header("Content-Length: " . $asset->size);
		header('Content-Disposition: attachment; filename="' . $asset->name . '"');
		header("Pragma: no-cache");
		header("Expires: 0");
		readfile($asset->getPath());
	}
	
	public function actionImport() {
		echo "<h1>Importing files:</h1>";
		foreach(glob(Yii::app()->basePath.'/data/test/*') as $file) {
			$asset = Asset::createFromFile($file);
			if(!$asset->save()) {
				throw new CException('Cannot save asset'.print_r($asset->getErrors(), true));
			}
			unlink($file);
			echo("<p>Imported ".$asset->uid . ' - '. $asset->name."</p>");
		}
		echo "<p>Done!</p>";
	}

}